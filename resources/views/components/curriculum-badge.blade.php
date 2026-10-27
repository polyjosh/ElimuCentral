{{--
    <x-curriculum-badge curriculum="cbe" />   ● CBE   (green, round marker)
    <x-curriculum-badge curriculum="844" />   ◆ 8-4-4 (neutral, diamond marker — phasing out)
    Shape and fill differ, so the two read apart without colour.
--}}
@props(['curriculum'])

@php $isCbe = strtolower((string) $curriculum) === 'cbe'; @endphp

<span {{ $attributes->class([
    'inline-flex h-6 items-center gap-1.5 whitespace-nowrap rounded-md px-2 text-xs font-bold tracking-[.02em]',
    'bg-primary-soft text-primary-soft-fg shadow-[inset_0_0_0_1px_rgb(var(--primary-text))]' => $isCbe,
    'bg-surface text-text-2 shadow-[inset_0_0_0_1px_rgb(var(--border-strong))]' => ! $isCbe,
]) }}>
    <span @class(['size-2 shrink-0 bg-current', 'rounded-full' => $isCbe, 'rotate-45 rounded-[1px]' => ! $isCbe]) aria-hidden="true"></span>
    {{ $isCbe ? 'CBE' : '8-4-4' }}
</span>
