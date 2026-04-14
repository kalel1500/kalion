@php /** @var \Illuminate\View\ComponentSlot $slot */ @endphp
@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @method('DELETE')
    <x-kal::button type="submit" {{ $attributes->except('action') }}/>
</form>
