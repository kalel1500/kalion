@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props(['value', 'for' => null])

<label for="{{ $for }}" class="block mb-2.5 text-sm font-medium text-heading">
    {{ $value ?? $slot }}
</label>
