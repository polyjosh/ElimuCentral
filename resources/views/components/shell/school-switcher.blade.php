{{--
    Shows the current school (always visible, so nobody records marks in the wrong school).
    Users linked to several schools get a menu; switching is a POST that changes
    session('school_id') and redirects to the dashboard.
    Route: Route::post('/school', SwitchSchoolController::class)->name('school.switch');
--}}
@php $multiple = isset($schools) && $schools->count() > 1; @endphp

<div x-data="{ open: false }" class="relative" x-on:click.outside="open = false" x-on:keydown.escape="open = false">
    <button
        type="button"
        @if ($multiple) x-on:click="open = ! open" x-bind:aria-expanded="open.toString()" aria-haspopup="true" @else disabled @endif
        class="flex min-h-[52px] w-full items-center gap-2.5 rounded-[10px] border border-border bg-surface p-2 text-left enabled:hover:bg-surface-2 disabled:cursor-default"
    >
        <x-shell.mark class="size-7" />
        <span class="min-w-0 flex-1">
            <b class="block truncate text-sm">{{ $school->short_name }}</b>
            <span class="block truncate text-xs text-muted">{{ $school->county }} County</span>
        </span>
        @if ($multiple)
            <x-lucide-chevrons-up-down class="size-4 text-muted" aria-hidden="true" />
        @endif
    </button>

    @if ($multiple)
        <div x-show="open" x-cloak class="absolute left-0 top-full z-40 mt-2 w-[300px] rounded-lg border border-border bg-surface p-2 shadow-lg">
            <p class="px-3 py-2 text-xs font-semibold uppercase tracking-[.06em] text-muted">{{ __('Your schools') }}</p>
            @foreach ($schools as $option)
                <form method="POST" action="{{ route('school.switch') }}">
                    @csrf
                    <input type="hidden" name="school_id" value="{{ $option->id }}">
                    <button
                        type="submit"
                        @if ($option->id === $school->id) aria-current="true" @endif
                        @class(['flex min-h-touch w-full items-center gap-3 rounded px-3 py-1.5 text-left text-sm hover:bg-surface-2', 'bg-primary-soft' => $option->id === $school->id])
                    >
                        <x-avatar :name="$option->name" size="sm" />
                        <span class="flex-1"><b class="block">{{ $option->name }}</b><span class="text-xs text-muted">{{ $option->pivot->role_label ?? '' }} · {{ $option->county }}</span></span>
                        @if ($option->id === $school->id)
                            <x-lucide-check class="size-4 text-primary-text" aria-hidden="true" />
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    @endif
</div>
