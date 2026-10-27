{{--
    Academic year / term switcher. The selected term lives in the session
    (session('term_id')), set by a plain POST, so every server-rendered page and
    Livewire component reads the same term. Changing term reloads the page.

    <x-term-switcher :terms="$terms" :current="$currentTerm" />

    $terms: collection of Term models with ->id, ->name ("Term 3"), ->year (2026),
            ->opens_on, ->closes_on (dates), ->status ('closed' | 'current' | 'upcoming').
    Route:  Route::post('/term', SwitchTermController::class)->name('term.switch');
--}}
@props(['terms', 'current'])

@php $byYear = collect($terms)->groupBy('year')->sortKeys(); @endphp

<div x-data="{ open: false, year: @js($current->year) }" class="relative" x-on:click.outside="open = false" x-on:keydown.escape="open = false">
    <x-button
        variant="secondary"
        size="sm"
        icon="calendar"
        x-on:click="open = ! open"
        x-bind:aria-expanded="open.toString()"
        aria-haspopup="true"
    >
        {{ $current->year }} · {{ $current->name }}
        <x-lucide-chevron-down class="size-4" aria-hidden="true" />
    </x-button>

    <div x-show="open" x-cloak class="absolute right-0 top-full z-40 mt-2 w-[340px] max-w-[calc(100vw-32px)] rounded-lg border border-border bg-surface p-2 shadow-md">
        <div class="mb-2 flex gap-0.5 rounded-[10px] bg-surface-3 p-[3px]" role="group" aria-label="{{ __('Year') }}">
            @foreach ($byYear->keys() as $year)
                <button
                    type="button"
                    class="min-h-[38px] flex-1 rounded-lg text-sm font-semibold text-muted"
                    x-bind:class="year === {{ $year }} && 'bg-surface text-text shadow-sm'"
                    x-bind:aria-pressed="(year === {{ $year }}).toString()"
                    x-on:click="year = {{ $year }}"
                >{{ $year }}</button>
            @endforeach
        </div>

        @foreach ($byYear as $year => $yearTerms)
            <ul x-show="year === {{ $year }}" class="flex flex-col gap-0.5">
                @foreach ($yearTerms as $term)
                    <li>
                        <form method="POST" action="{{ route('term.switch') }}">
                            @csrf
                            <input type="hidden" name="term_id" value="{{ $term->id }}">
                            <button
                                type="submit"
                                @class([
                                    'flex min-h-[52px] w-full items-center justify-between gap-3 rounded px-3 py-1.5 text-left hover:bg-surface-2',
                                    'bg-primary-soft hover:bg-primary-soft' => $term->id === $current->id,
                                ])
                                @if ($term->id === $current->id) aria-current="true" @endif
                            >
                                <span>
                                    <b class="block text-sm">{{ $term->name }}</b>
                                    <span class="text-xs text-muted tabular-nums">{{ $term->opens_on->format('d/m') }} – {{ $term->closes_on->format('d/m/Y') }}</span>
                                </span>
                                @if ($term->status === 'current')
                                    <x-badge tone="success" icon="check">{{ __('Current') }}</x-badge>
                                @elseif ($term->status === 'closed')
                                    <x-badge>{{ __('Closed') }}</x-badge>
                                @else
                                    <x-badge tone="info">{{ __('Upcoming') }}</x-badge>
                                @endif
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endforeach
    </div>
</div>
