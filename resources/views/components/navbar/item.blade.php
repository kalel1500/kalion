@props(['dropdown', 'tooltip', 'text', 'user' => false])

@php
    $hasDropdown = isset($dropdown);
    $hasTooltip = isset($tooltip);
    $itemClasses = $user
        ? 'mx-3 flex rounded-full text-sm focus:ring-4 focus:ring-gray md:mr-0'
        : 'mr-1 rounded-lg p-2 text-body-subtle hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-gray';
@endphp

<button type="button"
        @if($hasDropdown)
            data-dropdown-toggle="dropdown-{{ $attributes->get('id') }}" class="{{ $itemClasses }}"
        @else
            {{ $attributes->merge(['class' => $itemClasses ]) }}
        @endif
        @if($hasTooltip) data-tooltip-target="tooltip-{{ $attributes->get('id') }}" @endif
        >
    <span class="sr-only">{{ $text ?? $tooltip }}</span>
    {{ $slot }}
</button>

@if($hasTooltip)
    <x-kal::tooltip id="tooltip-{{ $attributes->get('id') }}">
        {{ $tooltip }}
    </x-kal::tooltip>
@endif

@if($hasDropdown)
    {{ $dropdown }}
@endif
