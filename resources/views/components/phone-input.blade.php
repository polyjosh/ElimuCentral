{{--
    Kenyan mobile number with a fixed +254 prefix.

    <x-phone-input name="guardian_phone" label="Guardian's phone" wire:model.blur="guardian_phone" />

    Alpine (phoneInput) strips spaces, a leading 0 or a pasted +254 and groups digits 3-3-3
    while typing. The server stays authoritative: store App\Support\Format::phoneE164($value).
--}}
@props([
    'name',
    'label',
    'id' => null,
    'hint' => 'Safaricom, Airtel or Telkom number.',
    'required' => false,
])

@php
    $id ??= \Illuminate\Support\Str::slug(str_replace(['.', '[', ']'], '-', $name));
    $invalid = $errors->has($name);
    $describedBy = collect([$hint ? "{$id}-hint" : null, $invalid ? "{$id}-error" : null])->filter()->implode(' ');
@endphp

<x-field :name="$name" :label="$label" :id="$id" :hint="$hint" :required="$required">
    <div class="ec-group" x-data="phoneInput">
        <span class="ec-addon border-r border-border" aria-hidden="true">+254</span>
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="tel"
            inputmode="tel"
            autocomplete="tel-national"
            placeholder="712 345 678"
            x-on:input="format($event.target)"
            @required($required)
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($invalid) aria-invalid="true" @endif
            {{ $attributes->class('tabular-nums') }}
        />
    </div>
</x-field>
