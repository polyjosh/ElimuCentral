{{--
    Desktop: global search, term switcher, notifications, profile menu.
    Phones:  app bar with the school mark, page title, language toggle and notifications.
--}}
@props(['title' => null])

@php
    $user = auth()->user();
    $other = app()->getLocale() === 'sw' ? 'en' : 'sw';
@endphp

<header class="sticky top-0 z-30 flex min-h-[60px] flex-wrap items-center gap-2 border-b border-border bg-surface px-2 py-2 sm:px-4 lg:gap-3 lg:px-6">
    {{-- Phone app bar --}}
    <div class="flex min-w-0 flex-1 items-center gap-2 pl-2 lg:hidden">
        <x-shell.mark class="size-7" />
        <b class="truncate">{{ $title ?? $school->short_name }}</b>
    </div>

    {{-- Desktop search: a plain GET form so it works without JS; "/" focuses it.
         Searches names, admission no., UPI, phone and M-Pesa code on the results page. --}}
    <form method="GET" action="{{ route('search') }}" role="search" class="hidden max-w-[460px] flex-1 lg:block"
          x-data x-on:keydown.slash.window="if (! ['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) { $event.preventDefault(); $refs.q.focus() }">
        <label for="global-search" class="sr-only">{{ __('Search') }}</label>
        <div class="ec-group">
            <span class="flex items-center pl-3 text-muted"><x-lucide-search class="size-5" aria-hidden="true" /></span>
            <input id="global-search" name="q" type="search" x-ref="q" value="{{ request('q') }}"
                   placeholder="{{ __('Search learners, staff, receipts…') }}" autocomplete="off">
            <span class="flex items-center pr-3"><kbd class="rounded border border-b-2 border-border-strong px-1.5 text-xs font-semibold text-muted">/</kbd></span>
        </div>
    </form>

    <div class="ml-auto flex items-center gap-1 lg:gap-2">
        @if (isset($currentTerm) && $user?->can('switch-term'))
            <div class="hidden lg:block"><x-term-switcher :terms="$terms" :current="$currentTerm" /></div>
        @endif

        {{-- Language toggle on every screen: shared phones change hands. --}}
        <form method="POST" action="{{ route('locale.switch') }}">
            @csrf
            <input type="hidden" name="locale" value="{{ $other }}">
            <button type="submit" class="grid h-touch w-touch place-items-center rounded text-sm font-bold text-text-2 hover:bg-surface-2" lang="{{ $other }}"
                    aria-label="{{ $other === 'sw' ? 'Badilisha lugha kuwa Kiswahili' : 'Change language to English' }}">
                {{ strtoupper($other) }}
            </button>
        </form>

        @php $unread = $navCounts['notifications'] ?? 0; @endphp
        <a href="{{ route('notifications.index') }}" class="relative grid h-touch w-touch place-items-center rounded text-text-2 hover:bg-surface-2"
           aria-label="{{ trans_choice('Notifications, :count unread|Notifications, :count unread', $unread, ['count' => $unread]) }}">
            <x-lucide-bell class="size-5" aria-hidden="true" />
            @if ($unread > 0)
                <span class="absolute right-[5px] top-1.5 grid h-[18px] min-w-[18px] place-items-center rounded-full border-2 border-surface bg-danger-solid px-1 text-[11px] font-bold leading-none text-white tabular-nums" aria-hidden="true">{{ $unread > 9 ? '9+' : $unread }}</span>
            @endif
        </a>

        <x-shell.profile-menu />
    </div>
</header>
