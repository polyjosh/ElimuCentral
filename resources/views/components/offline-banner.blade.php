{{--
    Shown by the $store.net Alpine store when the browser goes offline.
    Place once at the top of <body>. Pages with an offline queue (marks, attendance)
    show how many changes are waiting via $store.net.pending.
--}}
@props([
    'message' => __("You're offline. Your marks are saved on this device and will sync when you're back online."),
])

<div
    x-data
    x-show="! $store.net.online"
    x-cloak
    role="status"
    aria-live="polite"
    {{ $attributes->class('sticky top-0 z-40 flex items-start gap-2.5 bg-inverse px-4 py-2.5 text-sm text-inverse-fg') }}
>
    <x-lucide-wifi-off class="mt-px size-5 shrink-0" aria-hidden="true" />
    <span class="flex-1">{{ $message }}</span>
    <span
        x-show="$store.net.pending > 0"
        class="whitespace-nowrap rounded-full border border-current px-2 text-xs font-semibold leading-5 tabular-nums"
        x-text="$store.net.pending + ' {{ __('waiting') }}'"
    ></span>
</div>
