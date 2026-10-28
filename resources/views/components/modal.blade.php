{{--
    Confirm / form modal. Becomes a bottom sheet on phones.

    Open from anywhere:  $dispatch('open-modal', 'send-reminders')   (Alpine)
                         $this->dispatch('open-modal', 'send-reminders')   (Livewire)
    Or bind to Livewire: <x-modal name="x" wire:model="showConfirm">

    <x-modal name="send-reminders" title="Send 48 fee reminders?">
        Guardians of 48 learners …
        <x-slot:footer>
            <x-button variant="secondary" x-on:click="close()">Cancel</x-button>
            <x-button wire:click="send" loading="send">Send 48 SMS</x-button>
        </x-slot:footer>
    </x-modal>

    Titles are questions; buttons repeat the verb ("Send 48 SMS"), never "OK".
    Focus is trapped (Alpine focus plugin, bundled with Livewire), Esc closes and focus
    returns to the trigger.
--}}
@props(['name', 'title', 'maxWidth' => 'max-w-[480px]'])

@php $wireModel = $attributes->wire('model'); @endphp

<div
    x-data="{
        open: @if ($wireModel->value()) @entangle($wireModel) @else false @endif,
        close() { this.open = false },
    }"
    x-on:open-modal.window="if ($event.detail === @js($name)) open = true"
    x-on:close-modal.window="if ($event.detail === @js($name)) open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-end justify-center sm:items-center sm:p-6"
    {{ $attributes->whereDoesntStartWith('wire:model') }}
>
    <div class="absolute inset-0 bg-[rgb(18_18_16/.55)] dark:bg-black/65" x-on:click="close()" aria-hidden="true"></div>

    <div
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $name }}-title"
        x-trap.inert.noscroll="open"
        @class(['relative flex max-h-[90vh] w-full flex-col overflow-hidden rounded-t-xl bg-surface shadow-lg sm:rounded-xl', $maxWidth])
    >
        <header class="flex items-start justify-between gap-3 px-6 pb-2 pt-5">
            <h2 id="{{ $name }}-title" class="text-xl font-semibold">{{ $title }}</h2>
            <button type="button" class="-mr-2 -mt-1 grid h-touch w-touch shrink-0 place-items-center rounded text-text-2 hover:bg-surface-2" x-on:click="close()" aria-label="{{ __('Close') }}">
                <x-lucide-x class="size-5" aria-hidden="true" />
            </button>
        </header>

        <div class="overflow-y-auto px-6 pb-5 pt-2 text-[15px] text-text-2">
            {{ $slot }}
        </div>

        @isset($footer)
            <footer class="flex flex-wrap justify-end gap-3 border-t border-border bg-surface-2 px-6 py-4 [&>*]:max-sm:flex-1">
                {{ $footer }}
            </footer>
        @endisset
    </div>
</div>
