@php /** @var \Illuminate\View\ComponentSlot $slot */ @endphp
@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props(['value','link','href'])

<p class="text-sm font-light text-gray-500 dark:text-gray-400">
    {{ $value }} <x-kal::link :href="$href" :value="$link"/>
</p>
