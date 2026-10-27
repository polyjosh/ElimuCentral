{{-- Marks-entry grid. Alpine owns the cells; Livewire only receives batches (saveBatch). --}}
@php
    $assessment = $this->assessment;
    $learners = $assessment->stream->learners;
    $bandClasses = [
        'EE' => 'border-solid border-primary bg-primary text-primary-fg',
        'ME' => 'border-solid border-primary-text bg-primary-soft text-primary-soft-fg',
        'AE' => 'border-dashed border-accent bg-accent-soft text-accent-text',
        'BE' => 'border-dotted !border-2 border-danger bg-surface text-danger',
    ];
    $bars = ['EE' => 4, 'ME' => 3, 'AE' => 2, 'BE' => 1];
@endphp

<div
    class="flex flex-col gap-4"
    wire:ignore
    x-data="marksGrid({
        scope: 'marks:{{ $assessment->id }}',
        outOf: {{ (float) $assessment->out_of }},
        scale: @js($this->scale),
        rows: @js($this->gridRows),
        messages: {
            number: @js(__('Enter a number or AB')),
            max: @js(__('Max is :max')),
            conflict: @js(__('Someone else changed some marks. Their values are shown — check and re-enter if needed.')),
        },
    })"
    x-on:focusout="if (! $el.contains($event.relatedTarget)) flush()"
>
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <x-curriculum-badge :curriculum="$assessment->stream->curriculum" />
                <span class="text-sm text-muted">{{ $assessment->term->name }}, {{ $assessment->term->year }} · {{ ucfirst($assessment->type) }}</span>
            </div>
            <h1 class="text-3xl font-bold tracking-[-.015em]">{{ $assessment->learningArea->name }} — {{ $assessment->stream->name }}</h1>
        </div>

        {{-- Save status: Saving… → Saved 10:42 → Offline, 4 waiting --}}
        <div role="status" aria-live="polite">
            <x-badge tone="success" icon="cloud-check" x-show="status === 'saved'">
                {{ __('All changes saved') }}<span x-show="savedAt" x-text="' · ' + savedAt"></span>
            </x-badge>
            <x-badge tone="neutral" x-show="status === 'saving'" x-cloak><span class="ec-spinner !size-3 !border-2"></span>{{ __('Saving…') }}</x-badge>
            <x-badge tone="warning" icon="wifi-off" x-show="status === 'offline'" x-cloak>
                {{ __('Saved on this device') }}<span x-show="pending" x-text="' · ' + pending + ' {{ __('waiting') }}'"></span>
            </x-badge>
            <x-badge tone="danger" icon="triangle-alert" x-show="status === 'error'" x-cloak>{{ __("Couldn't save — retrying") }}</x-badge>
        </div>
    </div>

    <div class="overflow-auto rounded-lg border border-border bg-surface" style="max-height: 70vh">
        <table class="w-full border-separate border-spacing-0 text-sm">
            <caption class="sr-only">{{ __('Scores out of :total. Enter moves to the next learner.', ['total' => $assessment->out_of]) }}</caption>
            <thead>
                <tr class="[&>th]:sticky [&>th]:top-0 [&>th]:z-[1] [&>th]:border-b [&>th]:border-border [&>th]:bg-surface-2 [&>th]:px-2.5 [&>th]:py-2 [&>th]:text-left [&>th]:text-xs [&>th]:font-semibold [&>th]:text-text-2">
                    <th class="w-11">#</th>
                    <th>{{ __('Learner') }}</th>
                    <th class="w-[100px] !text-right">{{ __('Score') }} /{{ $assessment->out_of + 0 }}</th>
                    <th>{{ __('Level') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($learners as $learner)
                    <tr
                        wire:key="mark-{{ $learner->id }}"
                        class="[&>td]:h-12 [&>td]:border-b [&>td]:border-border [&>td]:px-2.5 focus-within:[&>td]:bg-accent-soft"
                    >
                        <td class="tabular-nums text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <label for="mark-{{ $learner->id }}" class="font-medium">{{ $learner->name }}</label>
                            <span class="block text-xs text-muted tabular-nums">{{ $learner->admission_number }}</span>
                        </td>
                        <td class="!p-1">
                            <input
                                id="mark-{{ $learner->id }}"
                                data-mark
                                type="text"
                                inputmode="decimal"
                                autocomplete="off"
                                enterkeyhint="next"
                                x-model="rows[{{ $learner->id }}].value"
                                x-on:input.debounce.250ms="changed({{ $learner->id }})"
                                x-on:keydown="onKeydown($event, {{ $learner->id }})"
                                x-on:focus="$event.target.select()"
                                x-bind:aria-invalid="rows[{{ $learner->id }}].error ? 'true' : null"
                                aria-describedby="mark-{{ $learner->id }}-msg"
                                class="h-10 w-full rounded-md border border-transparent bg-surface-2 px-2.5 text-right text-base font-semibold tabular-nums text-text focus:border-primary focus:bg-surface focus:outline focus:outline-[3px] focus:outline-ring aria-[invalid=true]:border-danger aria-[invalid=true]:bg-danger-bg aria-[invalid=true]:text-danger"
                            />
                        </td>
                        <td id="mark-{{ $learner->id }}-msg">
                            <template x-if="rows[{{ $learner->id }}].error">
                                <span class="text-[13px] font-medium text-danger" x-text="rows[{{ $learner->id }}].error"></span>
                            </template>
                            <template x-if="! rows[{{ $learner->id }}].error && String(rows[{{ $learner->id }}].value).toUpperCase() === 'AB'">
                                <x-badge>{{ __('Absent') }}</x-badge>
                            </template>
                            <template x-if="! rows[{{ $learner->id }}].error && rows[{{ $learner->id }}].level">
                                {{-- Client-side mirror of <x-rubric>; same four cues. --}}
                                <span
                                    class="inline-flex h-7 items-center gap-1.5 whitespace-nowrap rounded-md border-[1.5px] pl-[7px] pr-2 text-[13px] font-bold leading-none tabular-nums"
                                    x-bind:class="@js($bandClasses)[rows[{{ $learner->id }}].level.slice(0, 2)]"
                                >
                                    <span class="inline-flex h-3 items-end gap-0.5" aria-hidden="true">
                                        @foreach ([4, 6, 9, 12] as $i => $height)
                                            <i class="block w-[3px] rounded-[1px] bg-current" style="height: {{ $height }}px"
                                               x-bind:class="{ 'opacity-25': {{ $i }} >= @js($bars)[rows[{{ $learner->id }}].level.slice(0, 2)] }"></i>
                                        @endforeach
                                    </span>
                                    <span x-text="rows[{{ $learner->id }}].level"></span>
                                </span>
                            </template>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-sm text-muted tabular-nums" aria-live="polite">
            <b class="text-text" x-text="entered + ' / ' + total"></b> {{ __('entered') }}
        </p>
        <div class="flex flex-wrap gap-4 text-sm text-muted">
            <span><kbd class="rounded border border-b-2 border-border-strong px-1.5 text-xs font-semibold">Enter</kbd> {{ __('next learner') }}</span>
            <span><kbd class="rounded border border-b-2 border-border-strong px-1.5 text-xs font-semibold">↑</kbd> {{ __('previous') }}</span>
            <span><kbd class="rounded border border-b-2 border-border-strong px-1.5 text-xs font-semibold">AB</kbd> {{ __('absent') }}</span>
            <span><kbd class="rounded border border-b-2 border-border-strong px-1.5 text-xs font-semibold">Esc</kbd> {{ __('undo cell') }}</span>
        </div>
    </div>
</div>
