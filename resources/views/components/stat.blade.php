{{--
    Stat tile with built-in loading, empty and error states.

    <x-stat label="Fees collected · Term 3" :value="Format::kesShort($collected)"
            :exact="Format::kes($collected)" :progress="0.70" hint="70.0% of KES 24.86M expected" />
    <x-stat label="Attendance today" state="empty" hint="No registers taken yet." />
    <x-stat label="M-Pesa today" state="error" retry="$refresh" />

    Inside a lazy Livewire component, render <x-stat state="loading" /> from placeholder().
--}}
@props([
    'label' => null,
    'value' => null,
    'exact' => null,
    'hint' => null,
    'icon' => null,
    'progress' => null,
    'state' => 'ready',
    'retry' => null,
    'tone' => 'default',
])

<div {{ $attributes->class([
    'flex min-w-0 flex-col gap-1 rounded-lg border px-5 py-4',
    'border-border bg-surface' => $tone === 'default' && $state !== 'error',
    'border-transparent bg-accent-soft' => $tone === 'accent',
    'border-danger-bd bg-surface' => $state === 'error',
]) }}>
    @if ($state === 'loading')
        <div aria-busy="true" aria-label="{{ __('Loading') }}" class="flex flex-col gap-2">
            <span class="ec-skeleton h-4 w-3/5"></span>
            <span class="ec-skeleton h-8 w-4/5"></span>
            <span class="ec-skeleton h-2 w-full"></span>
        </div>
    @else
        <span @class(['flex items-center gap-2 text-sm font-medium', 'text-muted' => $tone !== 'accent', 'text-accent-text' => $tone === 'accent'])>
            @if ($icon)
                <x-dynamic-component :component="'lucide-'.$icon" class="size-4 shrink-0" aria-hidden="true" />
            @endif
            {{ $label }}
        </span>

        @if ($state === 'error')
            <p class="flex items-center gap-1.5 text-sm font-medium text-danger">
                <x-lucide-triangle-alert class="size-4 shrink-0" aria-hidden="true" />
                {{ $hint ?? __("Couldn't load this.") }}
            </p>
            @if ($retry)
                <x-button variant="secondary" size="sm" class="self-start" wire:click="{{ $retry }}" :loading="$retry">{{ __('Try again') }}</x-button>
            @endif
        @elseif ($state === 'empty')
            <span class="text-[28px] font-bold leading-9 text-subtle">—</span>
            @if ($hint)
                <span class="text-[13px] text-muted">{{ $hint }}</span>
            @endif
        @else
            <span class="text-[28px] font-bold leading-9 tracking-[-.01em] tabular-nums" @if ($exact) title="{{ $exact }}" @endif>
                {{ $value }}
                @if ($exact)
                    <span class="sr-only">({{ $exact }})</span>
                @endif
            </span>
            @if (! is_null($progress))
                <div class="flex h-2 overflow-hidden rounded bg-surface-3" role="img" aria-label="{{ round($progress * 100) }}%">
                    <i class="block h-full bg-primary" style="width: {{ max(0, min(100, $progress * 100)) }}%"></i>
                </div>
            @endif
            @if ($hint)
                <span class="text-[13px] text-muted tabular-nums">{{ $hint }}</span>
            @endif
            {{ $slot }}
        @endif
    @endif
</div>
