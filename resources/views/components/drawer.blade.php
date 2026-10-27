{{--
    Quick-view drawer (payment detail, learner peek). Right side on desktop, full screen on phones.
    Same open/close events and focus rules as <x-modal>.

    <x-drawer name="payment" title="TEST12AB34" eyebrow="Payment"> … </x-drawer>
--}}
@props(['name', 'title', 'eyebrow' => null])

<div
    x-data="{ open: false, close() { this.open = false } }"
    x-on:open-drawer.window="if ($event.detail === @js($name)) open = true"
    x-on:close-drawer.window="if ($event.detail === @js($name)) open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex justify-end"
    {{ $attributes }}
>
    <div class="absolute inset-0 bg-[rgb(18_18_16/.55)] dark:bg-black/65" x-on:click="close()" aria-hidden="true"></div>

    <aside
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $name }}-title"
        x-trap.inert.noscroll="open"
        x-transition:enter="motion-safe:transition motion-safe:duration-150"
        x-transition:enter-start="motion-safe:translate-x-4 opacity-0"
        class="relative flex h-full w-full flex-col bg-surface shadow-lg sm:w-[400px]"
    >
        <header class="flex items-start justify-between gap-3 border-b border-border px-6 py-4">
            <div>
                @if ($eyebrow)
                    <p class="text-xs font-semibold uppercase tracking-[.06em] text-muted">{{ $eyebrow }}</p>
                @endif
                <h2 id="{{ $name }}-title" class="text-lg font-semibold tabular-nums">{{ $title }}</h2>
            </div>
            <button type="button" class="-mr-2 grid h-touch w-touch shrink-0 place-items-center rounded text-text-2 hover:bg-surface-2" x-on:click="close()" aria-label="{{ __('Close') }}">
                <x-lucide-x class="size-5" aria-hidden="true" />
            </button>
        </header>
        <div class="flex-1 overflow-y-auto px-6 py-4">
            {{ $slot }}
        </div>
        @isset($footer)
            <footer class="flex flex-wrap gap-3 border-t border-border px-6 py-4">{{ $footer }}</footer>
        @endisset
    </aside>
</div>
