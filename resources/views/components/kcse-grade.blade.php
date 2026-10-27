{{--
    <x-kcse-grade grade="B+" />
    Deliberately neutral: the letter already carries meaning.
--}}
@props(['grade'])

<span {{ $attributes->class('inline-grid h-7 min-w-9 place-items-center rounded-md bg-surface-2 px-1.5 text-[13px] font-bold tabular-nums shadow-[inset_0_0_0_1px_rgb(var(--border-strong))]') }}>{{ $grade }}</span>
