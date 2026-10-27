{{--
    Senior School pathway. Each has its own icon so it reads in greyscale.
    <x-pathway-badge pathway="stem" />
    pathway: stem | social_sciences | arts_sports
--}}
@props(['pathway'])

@php
    $pathways = [
        'stem' => ['tone' => 'info', 'icon' => 'flask-conical', 'label' => __('STEM')],
        'social_sciences' => ['tone' => 'accent', 'icon' => 'globe', 'label' => __('Social Sciences')],
        'arts_sports' => ['tone' => 'plum', 'icon' => 'music', 'label' => __('Arts & Sports Science')],
    ];
    $p = $pathways[$pathway] ?? null;
@endphp

@if ($p)
    <x-badge :tone="$p['tone']" :icon="$p['icon']" {{ $attributes }}>{{ $p['label'] }}</x-badge>
@endif
