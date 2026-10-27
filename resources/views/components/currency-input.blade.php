{{--
    KES amount. Raw while typing (the cursor never jumps), formatted with commas on blur.

    <x-currency-input name="amount" label="Amount" wire:model.blur="amount" />

    Store App\Support\Format::parseKes($value) — integer cents.
--}}
@props([
    'name',
    'label',
    'id' => null,
    'hint' => null,
    'required' => false,
    'labelSrOnly' => false,
])

@php
    $id ??= \Illuminate\Support\Str::slug(str_replace(['.', '[', ']'], '-', $name));
    $invalid = $errors->has($name);
    $describedBy = collect([$hint ? "{$id}-hint" : null, $invalid ? "{$id}-error" : null])->filter()->implode(' ');
@endphp

<x-field :name="$name" :label="$label" :id="$id" :hint="$hint" :required="$required" :label-sr-only="$labelSrOnly">
    <div class="ec-group" x-data="currencyInput">
        <span class="ec-addon border-r border-border" aria-hidden="true">KES</span>
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="text"
            inputmode="decimal"
            autocomplete="off"
            placeholder="0.00"
            x-on:focus="unformat($event.target)"
            x-on:blur="formatValue($event.target)"
            @required($required)
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($invalid) aria-invalid="true" @endif
            {{ $attributes->class('text-right tabular-nums') }}
        />
    </div>
</x-field>
