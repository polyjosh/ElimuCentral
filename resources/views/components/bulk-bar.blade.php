{{--
    Appears when rows are selected. On phones it sticks to the bottom, above the bottom nav.

    @if (count($selected))
        <x-bulk-bar :count="count($selected)" clear="clearSelection">
            <x-button size="sm" variant="secondary" wire:click="remind" icon="message-square">Send SMS reminder</x-button>
        </x-bulk-bar>
    @endif
--}}
@props(['count', 'clear' => null])

<div
    role="region"
    aria-label="{{ __('Bulk actions') }}"
    {{ $attributes->class('flex flex-wrap items-center gap-x-3 gap-y-2 rounded-[10px] bg-primary-soft px-3 py-2 text-sm font-semibold text-primary-soft-fg max-md:sticky max-md:bottom-[72px] max-md:z-30 max-md:shadow-md') }}
>
    <span class="tabular-nums" aria-live="polite">{{ trans_choice(':count selected|:count selected', $count, ['count' => number_format($count)]) }}</span>
    {{ $slot }}
    @if ($clear)
        <x-button variant="ghost" size="sm" wire:click="{{ $clear }}">{{ __('Clear selection') }}</x-button>
    @endif
</div>
