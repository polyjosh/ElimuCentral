{{-- One row of <x-data-table> as a card on phones. Pass `selected` to outline it. --}}
@props(['selected' => false])

<div {{ $attributes->class([
    'flex flex-col gap-2.5 rounded-lg border bg-surface',
    'border-border px-4 py-3.5' => ! $selected,
    'border-2 border-primary px-[15px] py-[13px]' => $selected,
]) }}>
    {{ $slot }}
</div>
