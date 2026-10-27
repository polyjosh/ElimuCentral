{{--
    Main layout. One layout for every breakpoint:
      lg and up  → sidebar + top bar
      below lg   → app bar + bottom nav (parents, teachers, learners) + "More" page

    <x-layouts.app title="Learners">
        <x-slot:header> <h1 …>Learners</h1> … actions … </x-slot:header>
        …page…
    </x-layouts.app>

    Works with full-page Livewire components too: set `#[Layout('components.layouts.app')]`.

    Expects (shared via a view composer): $school, $schools, $terms, $currentTerm,
    $navCounts (array), and auth()->user() with ->theme ('light'|'dark'|'system')
    and ->bottomNavRole() ('parent'|'teacher'|'learner'|null).
--}}
@props(['title' => null])

@php
    $user = auth()->user();
    $theme = $user?->theme ?? 'system';
    $bottomRole = $user?->bottomNavRole();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => $theme === 'dark'])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0F5634">
    <title>{{ $title ? $title.' · ' : '' }}{{ $school->short_name ?? 'ElimuCentral' }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    @if ($theme === 'system')
        {{-- Only JS on first paint: follow the phone's theme without a flash. --}}
        <script>if (matchMedia('(prefers-color-scheme: dark)').matches) document.documentElement.classList.add('dark')</script>
    @endif

    {{-- Self-host Inter (latin subset, 400–700 woff2, ~48 KB) in production; Google Fonts shown for brevity. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-dvh bg-bg">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[70] focus:rounded focus:bg-surface focus:px-4 focus:py-2 focus:shadow-lg">{{ __('Skip to content') }}</a>

    <x-offline-banner />

    <div class="flex min-h-dvh">
        <x-shell.sidebar class="hidden lg:flex" />

        <div class="flex min-w-0 flex-1 flex-col">
            <x-shell.topbar :title="$title" />

            <main id="main" @class(['mx-auto flex w-full max-w-content flex-1 flex-col gap-6 p-4 sm:p-6', 'pb-24 lg:pb-6' => $bottomRole])>
                @isset($header)
                    <div class="flex flex-wrap items-end justify-between gap-4">{{ $header }}</div>
                @endisset

                {{ $slot }}
            </main>
        </div>
    </div>

    @if ($bottomRole)
        <x-shell.bottom-nav :role="$bottomRole" class="lg:hidden" />
    @endif

    <x-toast-stack />
    {{-- Livewire + Alpine are started from resources/js/app.js so our Alpine data registers first. --}}
    @livewireScriptConfig
</body>
</html>
