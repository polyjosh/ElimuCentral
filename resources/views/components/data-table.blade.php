{{--
    Table on md+ and a card list below 768 px, rendered from the same data.
    Header cells stick while the wrapper scrolls.

    <x-data-table :max-height="'640px'">
        <x-slot:head>
            <th class="w-10"><input type="checkbox" …></th>
            <th aria-sort="ascending">Learner ↑</th>
            <th class="text-right">Balance (KES)</th>
        </x-slot:head>

        @foreach ($learners as $learner)
            <tr wire:key="row-{{ $learner->id }}" @class(['is-selected' => in_array($learner->id, $selected)])> … </tr>
        @endforeach

        <x-slot:cards>
            @foreach ($learners as $learner)
                <x-data-table.card wire:key="card-{{ $learner->id }}"> … </x-data-table.card>
            @endforeach
        </x-slot:cards>
    </x-data-table>

    Numeric columns: `text-right tabular-nums`. The sorted column carries aria-sort.
--}}
@props(['maxHeight' => null])

<div {{ $attributes->class('min-w-0') }}>
    <div
        class="hidden overflow-auto rounded-lg border border-border bg-surface md:block"
        @if ($maxHeight) style="max-height: {{ $maxHeight }}" @endif
    >
        <table class="w-full border-separate border-spacing-0 text-sm [&_td]:border-b [&_td]:border-border [&_td]:px-3 [&_td]:py-2.5 [&_td]:align-middle [&_th]:sticky [&_th]:top-0 [&_th]:z-[1] [&_th]:whitespace-nowrap [&_th]:border-b [&_th]:border-border [&_th]:bg-surface-2 [&_th]:px-3 [&_th]:py-2.5 [&_th]:text-left [&_th]:text-[13px] [&_th]:font-semibold [&_th]:text-text-2 [&_tr:last-child_td]:border-b-0 [&_tr.is-selected_td]:bg-primary-soft [&_tr:hover_td]:bg-surface-2 [&_tr.is-selected:hover_td]:bg-primary-soft [&_input[type=checkbox]]:size-[18px] [&_input[type=checkbox]]:accent-[rgb(var(--primary))]">
            @isset($head)
                <thead><tr>{{ $head }}</tr></thead>
            @endisset
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @isset($cards)
        <div class="flex flex-col gap-2.5 md:hidden">
            {{ $cards }}
        </div>
    @endisset
</div>
