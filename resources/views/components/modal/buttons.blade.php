@php /** @var \Illuminate\View\ComponentSlot $slot */ @endphp
@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props([
    'modalId',
    'declarative',
    'showConfirmButton' => true,
    'showCancelButton'  => true,
    'showDenyButton'    => false,
    'confirmText'       => null,
    'cancelText'        => null,
    'denyText'          => null,
    'confirmVariant'    => 'brand',     // brand | secondary | tertiary | success | danger | warning | dark | ghost
    'cancelVariant'     => 'secondary', // brand | secondary | tertiary | success | danger | warning | dark | ghost
    'denyVariant'       => 'danger',    // brand | secondary | tertiary | success | danger | warning | dark | ghost
])

{{-- Confirm --}}
@if($showConfirmButton)
    <x-kal::button
        :data-modal-hide="$declarative ? $modalId : null"
        :data-fmodal-confirm="!$declarative ? $modalId : null"
        variant="{{ $confirmVariant }}"
    >{{ $confirmText }}</x-kal::button>
@endif

{{-- Deny --}}
@if($showDenyButton)
    <x-kal::button
        :data-modal-hide="$declarative ? $modalId : null"
        :data-fmodal-deny="!$declarative ? $modalId : null"
        variant="{{ $denyVariant }}"
    >{{ $denyText }}</x-kal::button>
@endif

{{-- Cancel --}}
@if($showCancelButton)
    <x-kal::button
        :data-modal-hide="$declarative ? $modalId : null"
        :data-fmodal-cancel="!$declarative ? $modalId : null"
        variant="{{ $cancelVariant }}"
    >{{ $cancelText }}</x-kal::button>
@endif
