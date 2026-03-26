@props(['href', 'dropdown', 'tooltip', 'id' => ''])

@php
    $isDropdown = isset($dropdown);
    $hasTooltip = isset($tooltip);
    $itemClasses = 'inline-flex cursor-pointer justify-center rounded-sm p-2 text-body-subtle hover:bg-neutral-tertiary-medium hover:text-heading';
@endphp

@if(!$isDropdown)

    <a href="{{ $href }}" @if($hasTooltip) data-tooltip-target="tooltip-{{ $id }}" @endif class="{{ $itemClasses }}">
        {{ $slot }}
    </a>

    @if($hasTooltip)
        <x-kal::tooltip id="tooltip-{{ $id }}">{{ $tooltip }}</x-kal::tooltip>
    @endif

@else

    <button type="button" data-dropdown-toggle="dropdown-{{ $id }}" class="{{ $itemClasses }}">
        {{ $slot }}
    </button>
    <!-- Dropdown -->
    <div class="z-50 my-4 hidden list-none divide-y divide-gray-100 rounded-sm bg-neutral-primary-strong text-base shadow-sm" id="dropdown-{{ $id }}">
        <ul class="py-1" role="none">
            {{ $dropdown }}
        </ul>
    </div>

@endif
