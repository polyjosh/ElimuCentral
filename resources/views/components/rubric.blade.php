{{--
    CBE rubric level chip. Four cues, never colour alone:
      1. the code (EE1…BE2), 2. signal bars 4/3/2/1,
      3. border style solid fill → soft → dashed → dotted, 4. colour.
    Screen readers hear the full label: "Meeting Expectations, level 1, 6 points".

    <x-rubric level="ME1" />
    <x-rubric level="EE" size="lg" />
    <x-rubric :level="\App\Support\Rubric::fromScore($score, $outOf)" />
--}}
@props(['level' => null, 'size' => 'md'])

@if (blank($level))
    <span {{ $attributes->class('text-xs text-subtle') }}>—</span>
@else
    @php
        $meta = \App\Support\Rubric::meta($level);

        $bands = [
            'ee' => 'border-solid border-primary bg-primary text-primary-fg',
            'me' => 'border-solid border-primary-text bg-primary-soft text-primary-soft-fg',
            'ae' => 'border-dashed border-accent bg-accent-soft text-accent-text',
            'be' => 'border-dotted border-danger bg-surface text-danger !border-2',
        ];

        $sizes = [
            'sm' => 'h-[22px] gap-1 rounded-md px-1.5 text-[11px]',
            'md' => 'h-7 gap-1.5 rounded-md pl-[7px] pr-2 text-[13px]',
            'lg' => 'h-10 gap-2 rounded px-3 text-[15px]',
        ];
    @endphp

    <span
        title="{{ $meta['label'] }}"
        {{ $attributes->class([
            'inline-flex items-center whitespace-nowrap border-[1.5px] font-bold leading-none tracking-[.02em] tabular-nums',
            $bands[$meta['band']],
            $sizes[$size] ?? $sizes['md'],
        ]) }}
    >
        <span class="inline-flex h-3 items-end gap-0.5" aria-hidden="true">
            @foreach ([4, 6, 9, 12] as $i => $height)
                <i @class(['block w-[3px] rounded-[1px] bg-current', 'opacity-25' => $i >= $meta['bars']]) style="height: {{ $height }}px"></i>
            @endforeach
        </span>
        <span aria-hidden="true">{{ $meta['code'] }}</span>
        <span class="sr-only">{{ $meta['label'] }}</span>
    </span>
@endif
