@props(['method', 'action'])

@php
    $isRealMethod = strtoupper($method) === 'GET' || strtoupper($method) === 'POST';
    $realMethod = $isRealMethod ? $method : 'POST';
@endphp

<form
    method="{{ $realMethod }}"
    action="{{ $action }}"
    {{ $attributes->only('class')->mergeTailwind('space-y-4 md:space-y-6') }}
>
    @csrf
    @if(!$isRealMethod)
        @method($method)
    @endif
    {{ $slot }}
</form>