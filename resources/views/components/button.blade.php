{{--
    <x-button>Save marks</x-button>
    <x-button variant="secondary" icon="download" href="/export">Export</x-button>
    <x-button wire:click="save" loading="save" loading-text="Saving…">Save marks</x-button>

    variant: primary | secondary | ghost | accent | danger
    size:    sm (36px, desktop toolbars only) | md (44px) | lg (52px) | xl (60px, full width)
    loading: the Livewire action name; disables the button and swaps the label while it runs.
--}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'icon' => null,
    'loading' => null,
    'loadingText' => null,
])

@php
    $variants = [
        'primary' => 'border-transparent bg-primary text-primary-fg hover:bg-primary-hover',
        'secondary' => 'border-border-strong bg-surface text-text hover:bg-surface-2',
        'ghost' => 'border-transparent bg-transparent text-primary-text hover:bg-primary-soft',
        'accent' => 'border-transparent bg-accent text-accent-fg hover:bg-accent-hover',
        'danger' => 'border-transparent bg-danger-solid text-white hover:brightness-90',
    ];

    $sizes = [
        'sm' => 'min-h-9 gap-1.5 rounded-md px-3 text-sm',
        'md' => 'min-h-touch gap-2 rounded px-4 text-[15px]',
        'lg' => 'min-h-[52px] gap-2 rounded-[10px] px-5 text-base',
        'xl' => 'min-h-[60px] w-full gap-2.5 rounded-lg px-6 text-lg font-bold',
    ];

    $iconSize = $size === 'sm' ? 'size-4' : ($size === 'xl' ? 'size-6' : 'size-5');

    $classes = implode(' ', [
        'inline-flex items-center justify-center border font-semibold leading-tight no-underline transition-colors duration-150',
        'disabled:cursor-not-allowed disabled:border-transparent disabled:bg-surface-3 disabled:text-subtle',
        'aria-disabled:pointer-events-none aria-disabled:border-transparent aria-disabled:bg-surface-3 aria-disabled:text-subtle',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icon)
            <x-dynamic-component :component="'lucide-'.$icon" class="{{ $iconSize }} shrink-0" aria-hidden="true" />
        @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button
        type="{{ $type }}"
        @if ($loading) wire:loading.attr="disabled" wire:target="{{ $loading }}" @endif
        {{ $attributes->class($classes) }}
    >
        @if ($loading)
            <span wire:loading.inline-flex wire:target="{{ $loading }}" class="ec-spinner" aria-hidden="true"></span>
        @endif

        @if ($icon)
            {{-- The spinner replaces the leading icon, so the button width never jumps. --}}
            <span class="contents" @if ($loading) wire:loading.remove wire:target="{{ $loading }}" @endif>
                <x-dynamic-component :component="'lucide-'.$icon" class="{{ $iconSize }} shrink-0" aria-hidden="true" />
            </span>
        @endif

        @if ($loading)
            <span wire:loading.remove wire:target="{{ $loading }}">{{ $slot }}</span>
            <span wire:loading wire:target="{{ $loading }}">{{ $loadingText ?? $slot }}</span>
        @else
            <span>{{ $slot }}</span>
        @endif
    </button>
@endif
