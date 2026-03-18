@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

<x-kal::form.checkbox id="terms" required>
    <span class="font-light">{{ __('k::text.input.terms_part_1') }}</span>
    <x-kal::link href="#" :value="__('k::text.input.terms_part_2')" />
</x-kal::form.checkbox>
