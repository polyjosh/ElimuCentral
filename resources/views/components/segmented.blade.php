{{--
    Segmented control — a real radio group, so it posts in plain forms and works without JS.

    <x-segmented name="residence" label="Residence" :options="['day' => 'Day', 'boarding' => 'Boarding']"
                 wire:model.live="residence" />
--}}
@props([
    'name',
    'label',
    'options' => [],
    'value' => null,
    'labelSrOnly' => true,
])

<fieldset {{ $attributes->whereDoesntStartWith('wire:model')->class('inline-flex') }}>
    <legend @class(['mb-1.5 text-sm font-semibold text-text-2', 'sr-only' => $labelSrOnly])>{{ $label }}</legend>
    <div class="inline-flex gap-0.5 rounded-[10px] bg-surface-3 p-[3px]">
        @foreach ($options as $optionValue => $optionLabel)
            <label class="relative cursor-pointer">
                <input
                    type="radio"
                    name="{{ $name }}"
                    value="{{ $optionValue }}"
                    class="peer sr-only"
                    @checked((string) $value === (string) $optionValue)
                    {{ $attributes->whereStartsWith('wire:model') }}
                />
                <span class="flex min-h-[38px] items-center rounded-lg px-3.5 text-sm font-semibold text-muted peer-checked:bg-surface peer-checked:text-text peer-checked:shadow-sm peer-focus-visible:outline peer-focus-visible:outline-[3px] peer-focus-visible:outline-offset-2 peer-focus-visible:outline-ring">
                    {{ $optionLabel }}
                </span>
            </label>
        @endforeach
    </div>
</fieldset>
