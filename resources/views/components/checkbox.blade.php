{{--
    <x-checkbox name="notify" wire:model="notify">Send SMS to guardians</x-checkbox>
    The whole 44px row is the target; the box itself is 20px.
--}}
@props(['name' => null])

<label class="inline-flex min-h-touch cursor-pointer items-center gap-2.5 text-[15px] has-[:disabled]:cursor-not-allowed has-[:disabled]:text-subtle">
    <input
        type="checkbox"
        @if ($name) name="{{ $name }}" @endif
        {{ $attributes->class('m-0 size-5 shrink-0 accent-[rgb(var(--primary))]') }}
    />
    <span>{{ $slot }}</span>
</label>
