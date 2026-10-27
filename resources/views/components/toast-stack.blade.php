{{--
    Place once in the layout. Show a toast from Livewire:
        $this->dispatch('toast', message: 'Register saved. SMS sent to 3 guardians.', tone: 'success');
    or from Alpine:  $dispatch('toast', { message: '…', tone: 'error' })
    Flash a toast across a redirect:  session()->flash('toast', ['message' => '…', 'tone' => 'success']);

    Success hides after 6 s; errors stay until dismissed. Bottom-centre on phones
    (above the bottom nav), bottom-right on desktop.
--}}
<div
    x-data
    @if (session('toast')) x-init="$store.toasts.push(@js(session('toast')))" @endif
    class="pointer-events-none fixed inset-x-4 bottom-20 z-[60] flex flex-col items-center gap-2 lg:inset-x-auto lg:bottom-6 lg:right-6 lg:items-end"
>
    <div role="status" aria-live="polite" class="flex w-full flex-col items-center gap-2 lg:items-end">
        <template x-for="toast in $store.toasts.items.filter(t => t.tone !== 'error')" :key="toast.id">
            <div class="pointer-events-auto flex w-full max-w-[420px] items-start gap-3 rounded-lg bg-inverse px-4 py-3.5 text-[15px] text-inverse-fg shadow-lg">
                <x-lucide-circle-check class="mt-px size-5 shrink-0 text-[#6FD39C] dark:text-[#17703A]" aria-hidden="true" />
                <p class="flex-1" x-text="toast.message"></p>
                <button type="button" class="-my-1 -mr-1 grid size-8 shrink-0 place-items-center rounded opacity-80 hover:opacity-100" x-on:click="$store.toasts.dismiss(toast.id)" aria-label="{{ __('Dismiss') }}">
                    <x-lucide-x class="size-4" aria-hidden="true" />
                </button>
            </div>
        </template>
    </div>
    <div role="alert" class="flex w-full flex-col items-center gap-2 lg:items-end">
        <template x-for="toast in $store.toasts.items.filter(t => t.tone === 'error')" :key="toast.id">
            <div class="pointer-events-auto flex w-full max-w-[420px] items-start gap-3 rounded-lg bg-inverse px-4 py-3.5 text-[15px] text-inverse-fg shadow-lg">
                <x-lucide-triangle-alert class="mt-px size-5 shrink-0 text-[#FF8A80] dark:text-[#B42318]" aria-hidden="true" />
                <p class="flex-1" x-text="toast.message"></p>
                <button type="button" class="-my-1 -mr-1 grid size-8 shrink-0 place-items-center rounded opacity-80 hover:opacity-100" x-on:click="$store.toasts.dismiss(toast.id)" aria-label="{{ __('Dismiss') }}">
                    <x-lucide-x class="size-4" aria-hidden="true" />
                </button>
            </div>
        </template>
    </div>
</div>
