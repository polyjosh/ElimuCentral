{{--
    Pick one rubric level per strand. A real radio group: ← → move, keys 1–8 jump
    straight to EE1…BE2, and it posts without JS.

    <x-rubric-picker name="strands.{{ $strand->id }}" label="1.0 Numbers" wire:model="strands.{{ $strand->id }}" />
--}}
@props([
    'name',
    'label',
    'levels' => array_keys(\App\Support\Rubric::CBE_SCALE),
    'allowNotAssessed' => true,
])

@php
    $wire = $attributes->whereStartsWith('wire:model');
    $bands = [
        'ee' => 'peer-checked:border-primary peer-checked:bg-primary peer-checked:text-primary-fg',
        'me' => 'peer-checked:border-primary-text peer-checked:bg-primary-soft peer-checked:text-primary-soft-fg',
        'ae' => 'peer-checked:border-dashed peer-checked:border-accent peer-checked:bg-accent-soft peer-checked:text-accent-text',
        'be' => 'peer-checked:border-dotted peer-checked:border-danger peer-checked:text-danger',
    ];
@endphp

<fieldset
    {{ $attributes->whereDoesntStartWith('wire:model')->class('flex flex-col gap-2') }}
    x-data
    x-on:keydown="
        const n = parseInt($event.key, 10);
        if (n >= 1 && n <= {{ count($levels) }}) {
            const radio = $el.querySelectorAll('input[type=radio]')[n - 1];
            radio.checked = true;
            radio.dispatchEvent(new Event('change', { bubbles: true }));
            radio.focus();
        }
    "
>
    <legend class="mb-2 text-sm font-semibold text-text-2">{{ $label }}</legend>
    <div class="flex flex-wrap gap-1.5">
        @foreach ($levels as $code)
            @php $meta = \App\Support\Rubric::meta($code); @endphp
            <label class="cursor-pointer" title="{{ $meta['label'] }}">
                <input type="radio" name="{{ $name }}" value="{{ $code }}" class="peer sr-only" {{ $wire }} />
                <span @class([
                    'inline-flex min-h-touch items-center gap-1.5 rounded border-[1.5px] border-border-strong bg-surface px-3 text-[13px] font-bold tabular-nums text-muted',
                    'peer-focus-visible:outline peer-focus-visible:outline-[3px] peer-focus-visible:outline-offset-2 peer-focus-visible:outline-ring',
                    $bands[$meta['band']],
                ])>
                    <span class="inline-flex h-3 items-end gap-0.5" aria-hidden="true">
                        @foreach ([4, 6, 9, 12] as $i => $height)
                            <i @class(['block w-[3px] rounded-[1px] bg-current', 'opacity-25' => $i >= $meta['bars']]) style="height: {{ $height }}px"></i>
                        @endforeach
                    </span>
                    {{ $code }}
                    <span class="sr-only">{{ $meta['label'] }}</span>
                </span>
            </label>
        @endforeach

        @if ($allowNotAssessed)
            <label class="cursor-pointer">
                <input type="radio" name="{{ $name }}" value="" class="peer sr-only" {{ $wire }} />
                <span class="inline-flex min-h-touch items-center rounded border-[1.5px] border-border-strong bg-surface px-3 text-[13px] font-semibold text-muted peer-checked:border-text-2 peer-checked:text-text peer-focus-visible:outline peer-focus-visible:outline-[3px] peer-focus-visible:outline-offset-2 peer-focus-visible:outline-ring">
                    {{ __('Not assessed') }}
                </span>
            </label>
        @endif
    </div>
</fieldset>
