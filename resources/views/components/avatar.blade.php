{{--
    Initials avatar — no photo download on 3G. The tint comes from the name, so the same
    person always gets the same colour.

    <x-avatar name="Chebet Kiprono" />            32/40/56/96 px: size="sm|md|lg|xl"
    <x-avatar name="Chebet Kiprono" :src="$learner->photo_url" size="xl" />   (profile only)
--}}
@props(['name', 'size' => 'md', 'src' => null])

@php
    $initials = collect(preg_split('/\s+/', trim($name)))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('');
    $tints = [
        'bg-primary-soft text-primary-soft-fg',
        'bg-accent-soft text-accent-text',
        'bg-info-bg text-info',
        'bg-plum-bg text-plum',
    ];
    $tint = $tints[crc32($name) % count($tints)];
    $sizes = [
        'sm' => 'size-8 text-xs rounded-full',
        'md' => 'size-10 text-sm rounded-full',
        'lg' => 'size-14 text-lg rounded-full',
        'xl' => 'size-24 text-[30px] rounded-xl',
    ];
@endphp

@if ($src)
    <img src="{{ $src }}" alt="{{ $name }}" loading="lazy" decoding="async" {{ $attributes->class([$sizes[$size] ?? $sizes['md'], 'shrink-0 object-cover']) }}>
@else
    <span aria-hidden="true" {{ $attributes->class(['inline-grid shrink-0 place-items-center font-bold uppercase tracking-[.02em]', $tint, $sizes[$size] ?? $sizes['md']]) }}>{{ $initials }}</span>
@endif
