@php $user = auth()->user(); @endphp

<div x-data="{ open: false }" class="relative" x-on:click.outside="open = false" x-on:keydown.escape="open = false; $refs.trigger.focus()">
    <button
        type="button"
        x-ref="trigger"
        x-on:click="open = ! open"
        x-bind:aria-expanded="open.toString()"
        aria-haspopup="true"
        class="flex min-h-touch items-center gap-2 rounded px-1.5 hover:bg-surface-2"
    >
        <x-avatar :name="$user->name" size="sm" />
        <span class="hidden text-sm lg:inline">{{ $user->short_name ?? $user->name }}</span>
        <span class="sr-only lg:hidden">{{ __('Profile and settings') }}</span>
        <x-lucide-chevron-down class="hidden size-4 text-muted lg:block" aria-hidden="true" />
    </button>

    <div x-show="open" x-cloak class="absolute right-0 top-full z-40 mt-2 w-60 rounded-lg border border-border bg-surface p-2 shadow-lg">
        <div class="mb-1.5 border-b border-border px-3 pb-3 pt-2">
            <b class="block text-sm">{{ $user->name }}</b>
            <span class="text-xs text-muted">{{ $user->role_label }}</span>
        </div>
        <a href="{{ route('profile') }}" class="flex min-h-touch items-center rounded px-3 text-sm text-text no-underline hover:bg-surface-2">{{ __('My profile') }}</a>
        <form method="POST" action="{{ route('theme.switch') }}">
            @csrf
            <label for="theme-select" class="sr-only">{{ __('Theme') }}</label>
            <select id="theme-select" name="theme" onchange="this.form.submit()" class="min-h-touch w-full rounded bg-transparent px-3 text-sm hover:bg-surface-2">
                @foreach (['system' => __('Theme: System'), 'light' => __('Theme: Light'), 'dark' => __('Theme: Dark')] as $value => $label)
                    <option value="{{ $value }}" @selected(($user->theme ?? 'system') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex min-h-touch w-full items-center rounded px-3 text-left text-sm text-danger hover:bg-danger-bg">{{ __('Log out') }}</button>
        </form>
    </div>
</div>
