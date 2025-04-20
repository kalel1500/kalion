@props(['id' => 'alert', 'color' => 'gray'])

@php
    $arrayDivClasses = [
        'blue'      => 'text-blue-800   bg-blue-50   dark:text-blue-400   dark:border-blue-800   border-blue-300   ',
        'red'       => 'text-red-800    bg-red-50    dark:text-red-400    dark:border-red-800    border-red-300    ',
        'green'     => 'text-green-800  bg-green-50  dark:text-green-400  dark:border-green-800  border-green-300  ',
        'yellow'    => 'text-yellow-800 bg-yellow-50 dark:text-yellow-300 dark:border-yellow-800 border-yellow-300 ',
        'gray'      => 'text-gray-800   bg-gray-100  dark:text-gray-300   dark:border-gray-600   border-gray-300   ',
    ];
    $divClasses = $arrayDivClasses[$color];
    $arrayBtnClasses = [
        'blue'      => 'text-blue-500   bg-blue-50   dark:text-blue-400   focus:ring-blue-400   hover:bg-blue-200   ',
        'red'       => 'text-red-500    bg-red-50    dark:text-red-400    focus:ring-red-400    hover:bg-red-200    ',
        'green'     => 'text-green-500  bg-green-50  dark:text-green-400  focus:ring-green-400  hover:bg-green-200  ',
        'yellow'    => 'text-yellow-500 bg-yellow-50 dark:text-yellow-300 focus:ring-yellow-400 hover:bg-yellow-200 ',
        'gray'      => 'text-gray-500   bg-gray-100  dark:text-gray-300   focus:ring-gray-400   hover:bg-gray-200   ',
    ];
    $btnClasses = $arrayBtnClasses[$color];
@endphp

<div id="{{ $id }}" class="flex items-center p-4 mb-4 border-t-4 dark:bg-gray-800 {{ $divClasses }}" role="alert">
    {{--<svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg>--}}
    <x-kal::icon.information-circle flowbite />
    <div class="ms-3 text-sm font-medium">
        {{ $slot }}
    </div>
    <button type="button" class="ms-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 p-1.5 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:hover:bg-gray-700 {{ $btnClasses }}" data-dismiss-target="#{{ $id }}" aria-label="Close">
        <span class="sr-only">{{ __('Dismiss') }}</span>
        {{--<svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>--}}
        <x-kal::icon.x-mark />
    </button>
</div>
