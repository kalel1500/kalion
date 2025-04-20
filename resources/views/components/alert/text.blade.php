@props(['id' => 'alert', 'title' => 'Text title', 'color' => 'gray'])

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
        'blue'      => 'text-blue-800   border-blue-800   hover:bg-blue-900   focus:ring-blue-200   dark:border-blue-600   dark:hover:bg-blue-600   dark:text-blue-400   dark:hover:text-white    dark:focus:ring-blue-800',
        'red'       => 'text-red-800    border-red-800    hover:bg-red-900    focus:ring-red-300    dark:border-red-600    dark:hover:bg-red-600    dark:text-red-500    dark:hover:text-white    dark:focus:ring-red-800',
        'green'     => 'text-green-800  border-green-800  hover:bg-green-900  focus:ring-green-300  dark:border-green-600  dark:hover:bg-green-600  dark:text-green-400  dark:hover:text-white    dark:focus:ring-green-800',
        'yellow'    => 'text-yellow-800 border-yellow-800 hover:bg-yellow-900 focus:ring-yellow-300 dark:border-yellow-300 dark:hover:bg-yellow-300 dark:text-yellow-300 dark:hover:text-gray-800 dark:focus:ring-yellow-800',
        'gray'      => 'text-gray-800   border-gray-700   hover:bg-gray-800   focus:ring-gray-300   dark:border-gray-600   dark:hover:bg-gray-600   dark:text-gray-300   dark:hover:text-white    dark:focus:ring-gray-800',
    ];
    $btnClasses = $arrayBtnClasses[$color];
@endphp

<div id="{{ $id }}" class="p-4 mb-4 border rounded-lg dark:bg-gray-800 {{ $divClasses }}" role="alert">
    <div class="flex items-center">
        {{--<svg class="shrink-0 w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg>--}}
        <x-kal::icon.information-circle flowbite class="me-2" />
        <span class="sr-only">Info</span>
        <h3 class="text-lg font-medium">{{ $title }}</h3>
    </div>
    <div class="mt-2 mb-4 text-sm">
        {{ $slot }}
    </div>
    <div class="flex">
        <button type="button" class="bg-transparent border hover:text-white focus:ring-4 focus:outline-none font-medium rounded-lg text-xs px-3 py-1.5 text-center {{ $btnClasses }}" data-dismiss-target="#{{ $id }}" aria-label="Close">
            {{ __('Dismiss') }}
        </button>
    </div>
</div>
