@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props(['for'])

@php($messages = $errors->get($for))

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'mt-2.5 text-sm text-fg-danger-strong']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
