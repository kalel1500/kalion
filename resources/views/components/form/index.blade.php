@php /** @var \Illuminate\View\ComponentSlot $slot */ @endphp
@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props(['method', 'action'])

@php
    $isRealMethod = strtoupper($method) === 'GET' || strtoupper($method) === 'POST';
    $realMethod = $isRealMethod ? $method : 'POST';
@endphp

<form
    method="{{ $realMethod }}"
    action="{{ $action }}"
    {{ $attributes->twMerge('space-y-4 md:space-y-6') }}
>
    @csrf
    @if(!$isRealMethod)
        @method($method)
    @endif
    {{ $slot }}
</form>
