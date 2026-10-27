{{--
    Link tabs (?tab=fees) — each panel is server-rendered and works without JS.
    Scrolls sideways on phones rather than wrapping.

    <x-tabs label="Learner sections" :tabs="[
        'academics' => ['label' => 'Academics', 'href' => '?tab=academics'],
        'fees' => ['label' => 'Fees', 'href' => '?tab=fees', 'badge' => 'KES 18,750', 'tone' => 'danger'],
    ]" :active="request('tab', 'academics')" />
--}}
@props(['tabs' => [], 'active' => null, 'label'])

<nav aria-label="{{ $label }}" {{ $attributes->class('flex gap-1 overflow-x-auto border-b border-border') }}>
    @foreach ($tabs as $key => $tab)
        <a
            href="{{ $tab['href'] }}"
            @if ($key === $active) aria-current="page" @endif
            @class([
                '-mb-px inline-flex min-h-touch items-center gap-2 whitespace-nowrap border-b-2 px-3.5 text-[15px] no-underline',
                'border-primary font-semibold text-primary-text' => $key === $active,
                'border-transparent font-medium text-muted hover:text-text' => $key !== $active,
            ])
        >
            {{ $tab['label'] }}
            @isset($tab['badge'])
                <x-badge :tone="$tab['tone'] ?? 'neutral'" class="tabular-nums">{{ $tab['badge'] }}</x-badge>
            @endisset
        </a>
    @endforeach
</nav>
