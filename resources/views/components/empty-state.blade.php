{{--
    Empty, no-results and error states for any data region.

    <x-empty-state icon="user-plus" title="No learners in Grade 8 Blue"
                   description="Import your class list from Excel, or add learners one at a time.">
        <x-button href="{{ route('learners.import') }}">Import from Excel</x-button>
    </x-empty-state>

    <x-empty-state tone="danger" icon="triangle-alert" title="We couldn't load learners"
                   description="Check your connection and try again. Ref EC-4410." />
--}}
@props(['icon' => 'inbox', 'title', 'description' => null, 'tone' => 'primary'])

<div {{ $attributes->class('flex flex-col items-center gap-2.5 px-4 py-7 text-center') }}>
    <span @class([
        'mb-1 grid size-16 place-items-center rounded-full',
        'bg-primary-soft text-primary-text' => $tone === 'primary',
        'bg-surface-3 text-muted' => $tone === 'neutral',
        'bg-danger-bg text-danger' => $tone === 'danger',
    ])>
        <x-dynamic-component :component="'lucide-'.$icon" class="size-6" aria-hidden="true" />
    </span>
    <h3 class="text-lg font-semibold">{{ $title }}</h3>
    @if ($description)
        <p class="max-w-sm text-sm text-muted">{{ $description }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-1 flex flex-col items-center gap-1">{{ $slot }}</div>
    @endif
</div>
