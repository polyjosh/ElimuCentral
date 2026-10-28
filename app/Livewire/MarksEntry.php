<?php

namespace App\Livewire;

use App\Models\Assessment;
use App\Models\Mark;
use App\Support\Rubric;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;
use Livewire\Component;

/**
 * Marks entry for one assessment (stream × learning area × CAT / mid-term / end-term).
 *
 * The grid itself is Alpine (resources/js/ui/marks-grid.js); this component renders it once
 * and receives batches through saveBatch(). saveBatch is #[Renderless] so a save never
 * re-renders the table under the teacher's fingers.
 *
 * Assumed schema (adapt names to yours):
 *   assessments: id, stream_id, learning_area_id, term_id, type, out_of, locked_at
 *   marks:       id, assessment_id, learner_id, score (decimal, nullable), absent (bool),
 *                comment, entered_by, updated_at — unique(assessment_id, learner_id)
 */
#[Layout('components.layouts.app')]
class MarksEntry extends Component
{
    #[Locked]
    public int $assessmentId;

    public function mount(Assessment $assessment): void
    {
        $this->authorize('enterMarks', $assessment);
        $this->assessmentId = $assessment->id;
    }

    #[Computed]
    public function assessment(): Assessment
    {
        return Assessment::with(['stream.learners' => fn ($q) => $q->orderBy('name'), 'learningArea', 'term'])
            ->findOrFail($this->assessmentId);
    }

    /** Initial state for Alpine, keyed by learner id. */
    #[Computed]
    public function gridRows(): array
    {
        $marks = Mark::where('assessment_id', $this->assessmentId)->get()->keyBy('learner_id');

        return $this->assessment->stream->learners->mapWithKeys(function ($learner) use ($marks) {
            $mark = $marks->get($learner->id);
            $value = $mark ? ($mark->absent ? 'AB' : $this->formatScore($mark->score)) : '';

            return [$learner->id => [
                'value' => $value,
                'saved' => $value,
                'version' => $mark?->updated_at?->toIso8601String(),
                'level' => $mark && ! $mark->absent ? Rubric::fromScore($mark->score, $this->assessment->out_of) : null,
                'error' => '',
            ]];
        })->all();
    }

    /** Scale for the in-browser level preview; the server stays the source of truth. */
    #[Computed]
    public function scale(): array
    {
        return collect(Rubric::CBE_SCALE)
            ->map(fn ($band, $code) => ['code' => $code, 'min' => $band[0], 'max' => $band[1]])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array{id: int|string, value: string, version: ?string}>  $items
     * @return array{saved: array, conflicts: array, versions: array, current: array}
     */
    #[Renderless]
    public function saveBatch(array $items): array
    {
        $assessment = $this->assessment;
        $this->authorize('enterMarks', $assessment);

        if ($assessment->locked_at) {
            abort(423, __('This assessment has been submitted and is locked.'));
        }

        $learnerIds = $assessment->stream->learners->pluck('id')->all();
        $result = ['saved' => [], 'conflicts' => [], 'versions' => [], 'current' => []];

        DB::transaction(function () use ($items, $assessment, $learnerIds, &$result) {
            $existing = Mark::where('assessment_id', $assessment->id)
                ->whereIn('learner_id', collect($items)->pluck('id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('learner_id');

            foreach ($items as $item) {
                $learnerId = (int) $item['id'];
                if (! in_array($learnerId, $learnerIds, true)) {
                    continue;
                }

                $raw = strtoupper(trim((string) ($item['value'] ?? '')));
                $absent = $raw === 'AB';
                $score = $absent || $raw === '' ? null : (float) $raw;

                // Same rule as the browser: never store an invalid score.
                if ($score !== null && ($score < 0 || $score > $assessment->out_of)) {
                    continue;
                }

                $mark = $existing->get($learnerId);

                // Optimistic check: someone else saved after this device loaded the value.
                if ($mark && ($item['version'] ?? null) !== $mark->updated_at?->toIso8601String()) {
                    $result['conflicts'][] = $learnerId;
                    $result['current'][$learnerId] = $mark->absent ? 'AB' : $this->formatScore($mark->score);

                    continue;
                }

                if ($raw === '' && $mark) {
                    $mark->delete();
                    $result['saved'][] = $learnerId;
                    $result['versions'][$learnerId] = null;

                    continue;
                }

                if ($raw === '') {
                    $result['saved'][] = $learnerId;

                    continue;
                }

                $mark ??= new Mark(['assessment_id' => $assessment->id, 'learner_id' => $learnerId]);
                $mark->fill(['score' => $score, 'absent' => $absent, 'entered_by' => auth()->id()])->save();

                $result['saved'][] = $learnerId;
                $result['versions'][$learnerId] = $mark->updated_at->toIso8601String();
            }
        });

        return $result;
    }

    private function formatScore(?float $score): string
    {
        if ($score === null) {
            return '';
        }

        return rtrim(rtrim(number_format($score, 1, '.', ''), '0'), '.');
    }

    public function render()
    {
        return view('livewire.marks-entry')->title(
            $this->assessment->learningArea->name.' — '.$this->assessment->stream->name
        );
    }
}
