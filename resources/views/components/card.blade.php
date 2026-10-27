{{--
    <x-card title="Upcoming events">
        <x-slot:actions><a href="…">View calendar</a></x-slot:actions>
        …
    </x-card>
    Cards use a border, not a shadow, and never nest more than one level.
--}}
@props(['title' => null, 'padded' => true])

<section {{ $attributes->class('min-w-0 rounded-lg border border-border bg-surface') }}>
    @if ($title || isset($actions))
        <header class="flex flex-wrap items-center justify-between gap-2 border-b border-border px-4 py-4 sm:px-5">
            @if ($title)
                <h2 class="text-base font-semibold">{{ $title }}</h2>
            @endif
            @isset($actions)
                <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
            @endisset
        </header>
    @endif

    <div @class(['p-4 sm:p-5' => $padded])>
        {{ $slot }}
    </div>
</section>
