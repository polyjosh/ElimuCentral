{{--
    Livewire file upload (WithFileUploads). The native input stays in the DOM (visually
    hidden) so keyboard and screen readers use it; drag-and-drop is an enhancement.

    <x-file-upload wire:model="file" accept=".xlsx,.csv" hint=".xlsx or .csv, up to 5 MB" />
--}}
@props([
    'id' => 'upload-'.\Illuminate\Support\Str::random(6),
    'hint' => null,
    'label' => __('Drop a file here or'),
    'action' => __('choose one'),
])

@php $model = $attributes->wire('model')->value(); @endphp

<div
    x-data="{ dragging: false, progress: null, error: null }"
    x-on:livewire-upload-start="progress = 0; error = null"
    x-on:livewire-upload-progress="progress = $event.detail.progress"
    x-on:livewire-upload-finish="progress = null"
    x-on:livewire-upload-error="progress = null; error = @js(__('Upload failed. Check your connection and try again.'))"
    x-on:dragover.prevent="dragging = true"
    x-on:dragleave.prevent="dragging = false"
    x-on:drop.prevent="dragging = false; $refs.input.files = $event.dataTransfer.files; $refs.input.dispatchEvent(new Event('change', { bubbles: true }))"
    class="flex flex-col gap-2"
>
    <div
        class="flex flex-col items-center gap-2 rounded-lg border-2 border-dashed border-border-strong bg-surface px-5 py-7 text-center"
        x-bind:class="{ '!border-primary !bg-primary-soft': dragging, '!border-danger !bg-danger-bg': error || @js($errors->has($model)) }"
    >
        <x-lucide-upload class="size-6 text-primary-text" aria-hidden="true" />
        <p class="font-semibold">
            <span x-text="dragging ? @js(__('Release to upload')) : @js($label)"></span>
            <label for="{{ $id }}" class="cursor-pointer text-primary-text underline" x-show="! dragging">{{ $action }}</label>
        </p>
        @if ($hint)
            <p class="text-sm text-muted">{{ $hint }}</p>
        @endif
        <input id="{{ $id }}" type="file" x-ref="input" class="sr-only" {{ $attributes }} />
    </div>

    <div x-show="progress !== null" x-cloak class="flex flex-col gap-1.5">
        <div class="flex justify-between text-sm"><span class="font-semibold">{{ __('Uploading…') }}</span><span class="tabular-nums text-muted" x-text="progress + '%'"></span></div>
        <div class="h-2 overflow-hidden rounded bg-surface-3" role="progressbar" aria-valuemin="0" aria-valuemax="100" x-bind:aria-valuenow="progress" aria-label="{{ __('Upload progress') }}">
            <i class="block h-full bg-primary" x-bind:style="'width:' + progress + '%'"></i>
        </div>
    </div>

    <p x-show="error" x-cloak class="flex gap-1.5 text-[13px] font-medium text-danger" x-text="error"></p>
    @error($model)
        <p class="flex gap-1.5 text-[13px] font-medium text-danger">
            <x-lucide-circle-alert class="mt-px size-4 shrink-0" aria-hidden="true" />{{ $message }}
        </p>
    @enderror
</div>
