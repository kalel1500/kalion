@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props(['label', 'id' => null, 'name' => null, 'required' => false, 'disabled' => false])

@php($identifier = $id ?? $name ?? '')

<div class="flex items-center">
    <input
        type="checkbox"
        id="{{ $identifier }}"
        name="{{ $identifier }}"
        {{ $attributes->twMerge('w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft') }}
        @disabled($disabled)
        @required($required)
    >

    <label for="{{ $identifier }}" class="ms-2 text-sm font-medium text-heading select-none">
        {{ $label ?? $slot }}
        {{ $error ?? '' }}
    </label>
</div>
