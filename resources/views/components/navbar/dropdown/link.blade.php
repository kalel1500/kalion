@aware(['bigList', 'bigSquare'])
@props(['href' => '#', 'text', 'icon', 'time', 'is_post' => false])

@if($bigList)
    <a href="{{ $href }}" class="flex border-b border-gray-200 px-4 py-3 hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-600">
        {{ $icon ?? '' }}
        <div class="w-full pl-3">
            <div class="mb-1.5 text-sm font-normal text-gray-500 dark:text-gray-400">
                {{ $slot }}
            </div>
            @isset($time)
                <div class="text-blue-600 dark:text-blue-500 text-xs font-medium">{{ $time }}</div>
            @endisset
        </div>
    </a>
@elseif($bigSquare)
    <a href="{{ $href }}" class="group block rounded-lg p-4 text-center hover:bg-gray-100 dark:hover:bg-gray-600">
        <div class="mx-auto mb-1 h-7 w-7 text-gray-400 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-400">
            {{ $slot }}
        </div>
        <div class="text-sm text-gray-900 dark:text-white">{{ $text }}</div>
    </a>
@else
    <li>
        @php($itemClasses = "flex items-center text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white")
        @php($itemPaddingClasses = "px-4 py-2")
        @if($is_post)
            <form method="POST" action="{{ $href }}" class="{{ $itemClasses }}">
                @csrf
                <button class="flex items-center w-full rounded-sm cursor-pointer focus:outline-none focus:ring-2 focus:ring-gray-400 {{ $itemPaddingClasses }}">{{ $slot }}</button>
            </form>
        @else
            <a href="{{ $href }}" class="{{ $itemClasses . ' ' . $itemPaddingClasses }}">
                {{ $slot }}
            </a>
        @endif
    </li>
@endif



