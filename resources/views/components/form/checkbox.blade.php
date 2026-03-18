@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props(['label', 'id' => null, 'name' => null, 'required' => false, 'disabled' => false])

@php
    $id   = $id   ?? $name ?? '';
    $name = $name ?? $id   ?? '';
@endphp

<div>
    <div class="flex items-center">
        <input
            type="checkbox"
            id="{{ $id }}"
            name="{{ $name }}"
            {{ $attributes->twMerge('w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft') }}
            @disabled($disabled)
            @required($required)
        >

        <label for="{{ $id }}" class="ms-2 text-sm font-medium text-heading select-none">
            {{ $label ?? $slot }}
        </label>
    </div>
    <x-kal::form.error for="{{ $name }}" />
</div>
