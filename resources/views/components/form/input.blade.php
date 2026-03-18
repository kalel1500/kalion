@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props(['type', 'label' => null, 'id' => null, 'name' => null, 'value' => '', 'size' => 'base', 'required' => false, 'disabled' => false])

@php
    $id          = $id   ?? $name ?? '';
    $name        = $name ?? $id   ?? '';
    $sizeClasses = [
        'small' => 'text-sm   px-2.5 py-2',
        'base'  => 'text-sm   px-3   py-2.5',
        'large' => 'text-base px-3.5 py-3',
        'extra' => 'text-base px-4   py-3.5',
    ];
    $common     = 'border rounded-base block w-full shadow-xs' . ' ' . $sizeClasses[$size];
    $normal     = 'bg-neutral-secondary-medium border-default-medium text-heading focus:ring-brand focus:border-brand placeholder:text-body';
    $error      = 'bg-danger-soft border-danger-subtle text-fg-danger-strong focus:ring-danger focus:border-danger placeholder:text-fg-danger-strong';
    $classes    = $common.' '.($errors->has($name) ? $error : $normal);
@endphp

<x-kal::form.label for="{{ $id }}" :value="$label" />
@switch($type)
    @case('select')
        <select
            id="{{ $id }}"
            name="{{ $name }}"
            {{ $attributes->twMerge($classes) }}
            @disabled($disabled)
            @required($required)
        >
            {{ $slot }}
        </select>
        @break

    @case('textarea')
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
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
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ $value }}"
            {{ $attributes->twMerge($classes) }}
            @disabled($disabled)
            @required($required)
        />
@endswitch
<x-kal::form.error for="{{ $name }}" />
