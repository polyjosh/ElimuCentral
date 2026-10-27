{{--
    Native <select> — best picker on cheap Android and works without JS.
    For long lists (learners) use a server-searched combobox instead.

    <x-select name="stream_id" label="Stream" :options="$streams->pluck('name', 'id')"
              placeholder="Choose a stream" wire:model.live="stream_id" />
--}}
@props([
    'name',
    'label',
    'options' => [],
    'id' => null,
    'hint' => null,
    'placeholder' => null,
    'required' => false,
    'labelSrOnly' => false,
])

@php
    $id ??= \Illuminate\Support\Str::slug(str_replace(['.', '[', ']'], '-', $name));
    $invalid = $errors->has($name);
    $describedBy = collect([$hint ? "{$id}-hint" : null, $invalid ? "{$id}-error" : null])->filter()->implode(' ');
@endphp

<x-field :name="$name" :label="$label" :id="$id" :hint="$hint" :required="$required" :label-sr-only="$labelSrOnly">
    <div class="relative">
        <select
            id="{{ $id }}"
            name="{{ $name }}"
            @required($required)
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($invalid) aria-invalid="true" @endif
            {{ $attributes->class('ec-control pr-10') }}
        >
            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach ($options as $value => $text)
                <option value="{{ $value }}">{{ $text }}</option>
            @endforeach
            {{ $slot }}
        </select>
        <x-lucide-chevron-down class="pointer-events-none absolute right-3 top-1/2 size-5 -translate-y-1/2 text-muted" aria-hidden="true" />
    </div>
</x-field>
