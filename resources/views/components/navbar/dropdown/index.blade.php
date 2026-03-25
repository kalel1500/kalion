@aware(['id'])
@props(['header', 'footer', 'bigList' => false, 'bigSquare' => false])

@php
    $isBig = $bigList || $bigSquare;
    $isSquare = $bigSquare;
@endphp

<!-- Dropdown {{ $id }} -->
<div id="dropdown-{{ $id }}"
    @class([
        'z-50 my-4 hidden list-none rounded-sm bg-neutral-primary-strong text-base',
        'max-w-sm overflow-hidden shadow-lg divide-y divide-gray-100 dark:divide-gray-600' => $isBig,
        'w-56 shadow-sm' => !$isBig,
    ])
>
    @if(isset($header))
        <div @class([
                'block bg-gray-50 px-4 py-2 text-center text-base font-medium text-gray-700 dark:bg-gray-600 dark:text-gray-300' => $isBig,
                'px-4 py-3 border-b border-gray-100 dark:border-gray-600' => !$isBig,
            ])>
            {{ $header }}
        </div>
    @endif

    @if($isBig)
        <div @class(['grid grid-cols-3 gap-4 p-4' => $isSquare])>
            {{ $slot }}
        </div>
    @else
        <ul class="py-1 text-gray-700 dark:text-gray-300" aria-labelledby="dropdown">
            {{ $slot }}
        </ul>
    @endif


    @if($isBig && isset($footer))
        <a href="{{ $footer->attributes->get('href') ?? '#' }}" class="text-md block bg-gray-50 py-2 text-center font-medium text-gray-900 hover:bg-gray-100 dark:bg-gray-600 dark:text-white dark:hover:underline">
            <div class="inline-flex items-center">
                {{ $footer }}
            </div>
        </a>
    @endif
</div>
