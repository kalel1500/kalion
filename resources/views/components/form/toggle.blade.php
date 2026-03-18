@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props(['label', 'id' => null, 'name' => null, 'required' => false, 'disabled' => false])

@php($identifier = $id ?? $name ?? '')

<div>
    <label class="inline-flex items-center cursor-pointer">
        <input
            type="checkbox"
            id="{{ $identifier }}"
            name="{{ $identifier }}"
            value=""
            class="sr-only peer"
            @disabled($disabled)
            @required($required)
        >
        <div class="relative w-9 h-5 bg-neutral-quaternary peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-soft dark:peer-focus:ring-brand-soft rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-buffer after:content-[''] after:absolute after:top-0.5 after:inset-s-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
        <span class="select-none ms-3 text-sm font-medium text-heading">
            {{ $label ?? $slot }}
        </span>
    </label>
    <x-kal::form.error for="{{ $identifier }}" />
</div>
