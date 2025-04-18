@props(['value','link','href'])

<p class="text-sm font-light text-gray-500 dark:text-gray-400">
    {{ $value }} <x-kal::link :href="$href" :value="$link"/>
</p>