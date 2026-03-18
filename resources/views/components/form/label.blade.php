@php /** @var \Illuminate\View\ComponentSlot $slot */ @endphp
@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props(['value', 'for' => null])

@php($label = $value ?? $slot->toHtml())

@if(!empty(trim($label)))
    <label for="{{ $for }}" class="block mb-2.5 text-sm font-medium text-heading">
        {{ $label }}
    </label>
@endif
