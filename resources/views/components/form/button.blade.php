@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

<x-kal::button type="submit" {{ $attributes->twMerge('w-full text-center') }}>{{ $slot }}</x-kal::button>
