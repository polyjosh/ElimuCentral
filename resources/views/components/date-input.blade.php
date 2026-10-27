{{--
    DD/MM/YYYY date field with a calendar helper.

    <x-date-input name="closing_date" label="Closing date" wire:model="closing_date" />

    Typing comes first: "20112026", "20/11/2026" and "20-11-26" all normalise to 20/11/2026.
    The ISO value (2026-11-20) lives in a hidden input that carries wire:model, so Livewire
    always receives YYYY-MM-DD. Below md the calendar button opens the native date picker.

    Calendar keyboard: arrows move a day/week, PageUp/PageDown a month, Enter picks,
    Esc closes and returns focus to the field.
--}}
@props([
    'name',
    'label',
    'id' => null,
    'hint' => null,
    'required' => false,
    'min' => null,
    'max' => null,
])

@php
    $id ??= \Illuminate\Support\Str::slug(str_replace(['.', '[', ']'], '-', $name));
    $invalid = $errors->has($name);
    $describedBy = collect([$hint ? "{$id}-hint" : null, $invalid ? "{$id}-error" : null])->filter()->implode(' ');
    $wire = $attributes->whereStartsWith('wire:model');
@endphp

<x-field :name="$name" :label="$label" :id="$id" :hint="$hint" :required="$required">
    <div
        class="relative"
        x-data="datePicker({ min: @js($min), max: @js($max) })"
        x-on:keydown.escape.window="if (open) close()"
        x-on:click.outside="open && close()"
    >
        <input type="hidden" name="{{ $name }}" x-ref="iso" {{ $wire }} />

        <div class="ec-group">
            <input
                id="{{ $id }}"
                type="text"
                inputmode="numeric"
                autocomplete="off"
                placeholder="DD/MM/YYYY"
                x-ref="text"
                x-on:blur="commitText()"
                x-on:keydown.enter.prevent="commitText()"
                @required($required)
                @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
                x-bind:aria-invalid="invalid ? 'true' : null"
                @if ($invalid) aria-invalid="true" @endif
                {{ $attributes->whereDoesntStartWith('wire:model')->class('tabular-nums') }}
            />
            <button
                type="button"
                class="grid w-touch shrink-0 place-items-center border-l border-border text-text-2 hover:bg-surface-2"
                x-on:click="toggle()"
                x-bind:aria-expanded="open.toString()"
                aria-haspopup="dialog"
                aria-label="{{ __('Open calendar') }}"
            >
                <x-lucide-calendar class="size-5" aria-hidden="true" />
            </button>
            <input type="date" x-ref="native" class="sr-only" tabindex="-1" aria-hidden="true" x-on:change="pickNative($event.target.value)" />
        </div>

        <p x-show="invalid" x-cloak class="mt-1.5 text-[13px] font-medium text-danger" x-text="invalidMessage"></p>

        <div
            x-show="open"
            x-cloak
            x-trap="open"
            role="dialog"
            aria-modal="false"
            aria-label="{{ __('Choose :label', ['label' => $label]) }}"
            class="absolute left-0 top-full z-30 mt-2 w-[316px] max-w-[calc(100vw-32px)] rounded-lg border border-border bg-surface p-3 shadow-lg"
        >
            <div class="mb-2 flex items-center justify-between">
                <button type="button" class="grid h-touch w-touch place-items-center rounded hover:bg-surface-2" x-on:click="shiftMonth(-1)" aria-label="{{ __('Previous month') }}">
                    <x-lucide-chevron-left class="size-5" aria-hidden="true" />
                </button>
                <span class="font-semibold" x-text="monthLabel" aria-live="polite"></span>
                <button type="button" class="grid h-touch w-touch place-items-center rounded hover:bg-surface-2" x-on:click="shiftMonth(1)" aria-label="{{ __('Next month') }}">
                    <x-lucide-chevron-right class="size-5" aria-hidden="true" />
                </button>
            </div>

            <div class="grid grid-cols-7 text-center" role="grid" x-on:keydown="onGridKey($event)">
                <template x-for="d in weekdays" :key="d">
                    <span class="h-7 text-xs font-semibold leading-7 text-muted" role="columnheader" x-text="d"></span>
                </template>
                <template x-for="cell in cells" :key="cell.iso">
                    <button
                        type="button"
                        role="gridcell"
                        class="h-[42px] rounded text-sm tabular-nums hover:bg-surface-2 disabled:cursor-not-allowed disabled:opacity-40"
                        x-bind:class="{
                            'text-subtle': !cell.inMonth,
                            'font-bold shadow-[inset_0_0_0_1.5px_rgb(var(--primary))]': cell.isToday && !cell.isSelected,
                            'bg-primary text-primary-fg font-bold hover:bg-primary': cell.isSelected,
                        }"
                        x-bind:tabindex="cell.iso === focusIso ? 0 : -1"
                        x-bind:aria-selected="cell.isSelected.toString()"
                        x-bind:aria-label="cell.label"
                        x-bind:disabled="cell.disabled"
                        x-bind:data-iso="cell.iso"
                        x-on:click="pick(cell.iso)"
                        x-text="cell.day"
                    ></button>
                </template>
            </div>
        </div>
    </div>
</x-field>
