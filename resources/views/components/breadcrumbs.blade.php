{{--
    <x-breadcrumbs :items="['Learners' => route('learners.index'), 'Grade 8 Blue' => route('streams.show', $stream), 'Chebet Kiprono' => null]" />
    The last item (null URL) is the current page.
--}}
@props(['items' => []])

<nav aria-label="{{ __('Breadcrumb') }}" {{ $attributes }}>
    <ol class="flex flex-wrap items-center gap-1.5 text-sm text-muted">
        @foreach ($items as $label => $url)
            <li class="flex items-center gap-1.5">
                @if (! $loop->first)
                    <x-lucide-chevron-right class="size-4 shrink-0" aria-hidden="true" />
                @endif
                @if ($url)
                    <a href="{{ $url }}" class="text-muted no-underline hover:text-text hover:underline">{{ $label }}</a>
                @else
                    <span aria-current="page" class="font-semibold text-text">{{ $label }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
