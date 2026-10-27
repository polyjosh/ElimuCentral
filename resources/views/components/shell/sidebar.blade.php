{{--
    Desktop sidebar: school switcher + role-filtered navigation from config/navigation.php.
    Active item = request()->routeIs($route.'*').
--}}
@php
    $items = collect(config('navigation.sidebar'))
        ->filter(fn ($item) => empty($item['can']) || \Illuminate\Support\Facades\Gate::allows($item['can']));
    $counts = $navCounts ?? [];
@endphp

<aside aria-label="{{ __('Main navigation') }}" {{ $attributes->class('sticky top-0 h-dvh w-64 shrink-0 flex-col gap-1 overflow-y-auto border-r border-border bg-surface px-3 py-4') }}>
    <x-shell.school-switcher />

    <nav class="mt-2 flex flex-col gap-0.5">
        @foreach ($items as $item)
            @if (isset($item['group']))
                <p class="px-3 pb-1.5 pt-4 text-xs font-semibold uppercase tracking-[.06em] text-muted">{{ __($item['group']) }}</p>
            @else
                @php
                    $active = request()->routeIs($item['route'].'*');
                    $count = isset($item['count']) ? ($counts[$item['count']] ?? 0) : 0;
                @endphp
                <a
                    href="{{ route($item['route']) }}"
                    wire:navigate
                    @if ($active) aria-current="page" @endif
                    @class([
                        'flex min-h-touch items-center gap-3 rounded px-3 text-[15px] no-underline',
                        'bg-primary-soft font-semibold text-primary-soft-fg' => $active,
                        'font-medium text-text-2 hover:bg-surface-2' => ! $active,
                    ])
                >
                    <x-dynamic-component :component="'lucide-'.$item['icon']" class="size-5 shrink-0" aria-hidden="true" />
                    <span class="flex-1">{{ __($item['label']) }}</span>
                    @if ($count > 0)
                        <span class="rounded-full bg-accent-soft px-2 py-0.5 text-xs font-bold tabular-nums text-accent-text">
                            {{ $count }}<span class="sr-only"> {{ __('waiting') }}</span>
                        </span>
                    @endif
                </a>
            @endif
        @endforeach
    </nav>
</aside>
