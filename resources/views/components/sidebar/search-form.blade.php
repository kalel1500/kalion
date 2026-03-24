@props(['action' => '#'])

<form action="{{ $action }}" method="GET" class="mb-2 md:hidden">
    <label for="sidebar-search" class="sr-only">{{ __('Search') }}</label>
    <div class="relative">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            {{--<svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"></path></svg>--}}
            <x-kal::icon.magnifying-glass class="size-5 text-gray-500 dark:text-gray-400"/>
        </div>
        <input type="text" name="search" id="sidebar-search" class="focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500 block w-full rounded-lg border border-gray-300 bg-gray-50 p-2 pl-10 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400" placeholder="{{ __('Search') }}"/>
    </div>
</form>
