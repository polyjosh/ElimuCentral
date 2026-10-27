{{--
    Phone bottom navigation (hidden from lg up). Max 5 items, 64 px tall, icon + label
    always visible. Kiswahili labels wrap to two lines rather than truncate.
    Respects the gesture bar via env(safe-area-inset-bottom).
--}}
@props(['role'])

<nav
    aria-label="{{ __('Main') }}"
    {{ $attributes->class('fixed inset-x-0 bottom-0 z-30 flex border-t border-border bg-surface px-1 pb-[env(safe-area-inset-bottom)]') }}
>
    @foreach (config("navigation.bottom.{$role}", []) as $item)
        @php $active = request()->routeIs($item['route'].'*'); @endphp
        <a
            href="{{ route($item['route']) }}"
            wire:navigate
            @if ($active) aria-current="page" @endif
            @class([
                'flex min-h-16 min-w-0 flex-1 flex-col items-center justify-center gap-1 px-0.5 text-center text-xs leading-tight no-underline',
                'font-bold text-primary-text' => $active,
                'font-medium text-muted' => ! $active,
            ])
        >
            <span @class(['grid h-[30px] w-14 place-items-center rounded-full', 'bg-primary-soft' => $active])>
                <x-dynamic-component :component="'lucide-'.$item['icon']" class="size-5" aria-hidden="true" />
            </span>
            {{ __($item['label']) }}
        </a>
    @endforeach
</nav>
