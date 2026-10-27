{{--
    Wrapper shared by every form control: visible label, optional hint, error from $errors.
    The control inside must use the same id and point aria-describedby at "{id}-hint" and
    "{id}-error" so screen readers announce them (x-input, x-select etc. already do).

    <x-field name="upi" label="UPI number" hint="7 characters, e.g. 9KTR4M2" required>
        <input … />
    </x-field>
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
    $error = $errors->first($name);
@endphp

<div {{ $attributes->class('flex min-w-0 flex-col gap-1.5') }}>
    <label for="{{ $id }}" @class(['text-sm font-semibold text-text-2', 'sr-only' => $labelSrOnly])>
        {{ $label }}
        @if ($required)
            <span class="text-danger" aria-hidden="true">*</span>
        @endif
    </label>

    {{ $slot }}

    @if ($hint)
        <p id="{{ $id }}-hint" class="text-[13px] leading-[18px] text-muted">{{ $hint }}</p>
    @endif

    @if ($error)
        <p id="{{ $id }}-error" class="flex items-start gap-1.5 text-[13px] font-medium leading-[18px] text-danger">
            <x-lucide-circle-alert class="mt-px size-4 shrink-0" aria-hidden="true" />
            <span>{{ $error }}</span>
        </p>
    @endif
</div>
