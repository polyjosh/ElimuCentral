{{--
    <x-input name="full_name" label="Learner's full name" wire:model.blur="full_name"
             hint="As written on the birth certificate." required />

    Validate on blur, not on every keystroke (3G). Extra attributes go to the <input>.
--}}
@props([
    'name',
    'label',
    'id' => null,
    'hint' => null,
    'type' => 'text',
    'required' => false,
])

@php
    $id ??= \Illuminate\Support\Str::slug(str_replace(['.', '[', ']'], '-', $name));
    $invalid = $errors->has($name);
    $describedBy = collect([$hint ? "{$id}-hint" : null, $invalid ? "{$id}-error" : null])->filter()->implode(' ');
@endphp

<x-field :name="$name" :label="$label" :id="$id" :hint="$hint" :required="$required">
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @required($required)
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @if ($invalid) aria-invalid="true" @endif
        {{ $attributes->class('ec-control') }}
    />
</x-field>
