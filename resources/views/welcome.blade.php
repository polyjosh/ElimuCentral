{{-- Placeholder home. Phase 1 replaces this route with the sign-in screen (S1). --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0F5634">
    <title>ElimuCentral</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preload" href="/fonts/inter-latin-wght-normal.woff2" as="font" type="font/woff2" crossorigin>
    <script>if (matchMedia('(prefers-color-scheme: dark)').matches) document.documentElement.classList.add('dark')</script>
    @vite(['resources/css/app.css'])
</head>
<body class="grid min-h-dvh place-items-center bg-bg p-4">
    <main class="flex max-w-md flex-col items-center gap-4 text-center">
        <x-shell.mark class="size-14" />
        <h1 class="text-3xl font-bold">ElimuCentral</h1>
        <p class="text-text-2">{{ __('School management for Kenyan schools. Sign-in is coming soon.') }}</p>
        @if (app()->environment('local'))
            <x-button variant="secondary" icon="layout-grid" href="{{ route('ui') }}">{{ __('Open the UI kit') }}</x-button>
        @endif
    </main>
</body>
</html>
