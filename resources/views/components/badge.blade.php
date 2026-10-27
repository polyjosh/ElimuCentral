{{--
    Status badge. Always pair colour with a word, and usually an icon.

    <x-badge tone="success" icon="check">Paid</x-badge>
    <x-badge tone="danger" icon="x">Absent</x-badge>

    tone: neutral | success | warning | danger | info | primary | accent | plum
--}}
@props(['tone' => 'neutral', 'icon' => null])

@php
    $tones = [
        'neutral' => 'border-border bg-surface-2 text-text-2',
        'success' => 'border-success-bd bg-success-bg text-success',
        'warning' => 'border-warning-bd bg-warning-bg text-warning',
        'danger' => 'border-danger-bd bg-danger-bg text-danger',
        'info' => 'border-info-bd bg-info-bg text-info',
        'primary' => 'border-transparent bg-primary-soft text-primary-soft-fg',
        'accent' => 'border-transparent bg-accent-soft text-accent-text',
        'plum' => 'border-plum-bd bg-plum-bg text-plum',
    ];
@endphp

<span {{ $attributes->class(['inline-flex h-6 items-center gap-1 whitespace-nowrap rounded-full border px-2.5 text-xs font-semibold leading-none', $tones[$tone] ?? $tones['neutral']]) }}>
    @if ($icon)
        <x-dynamic-component :component="'lucide-'.$icon" class="size-3.5 shrink-0" aria-hidden="true" />
    @endif
    {{ $slot }}
</span>
