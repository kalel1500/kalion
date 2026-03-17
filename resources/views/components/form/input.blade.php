@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props(['type', 'id' => null, 'name' => null, 'value' => '', 'required' => false, 'disabled' => false])

@php
    $identifier = $id ?? $name ?? '';
    $common     = 'border text-sm rounded-base block w-full px-3 py-2.5 shadow-xs';
    $normal     = 'bg-neutral-secondary-medium border-default-medium text-heading focus:ring-brand focus:border-brand placeholder:text-body';
    $error      = 'bg-danger-soft border-danger-subtle text-fg-danger-strong focus:ring-danger focus:border-danger placeholder:text-fg-danger-strong';
    $classes    = $common.' '.($errors->has($name) ? $error : $normal);
@endphp

@switch($type)
    @case('select')
        <select
            id="{{ $identifier }}"
            name="{{ $identifier }}"
            {{ $attributes->twMerge($classes) }}
            @disabled($disabled)
            @required($required)
        >
            {{ $slot }}
        </select>
        @break

    @case(2)
        <textarea
            id="{{ $identifier }}"
            name="{{ $identifier }}"
            rows="{{ $rows }}"
            {{ $attributes->twMerge($classes . ' p-3.5') }}
            @disabled($disabled)
            @required($required)
        >
            {{ $value }}
        </textarea>
        @break

    @default
        <input
            type="{{ $type }}"
            id="{{ $identifier }}"
            name="{{ $identifier }}"
            value="{{ $value }}"
            {{ $attributes->twMerge($classes) }}
            @disabled($disabled)
            @required($required)
        />
@endswitch
