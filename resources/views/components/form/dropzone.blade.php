@php /** @var \Illuminate\View\ComponentSlot $slot */ @endphp
@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props([
    'withButton'  => false,
    'dropzoneId'  => 'dropzone-container' . uniqid(),
    'inputId'     => 'dropzone-input-' . uniqid(),
    'title'       => null,
    'description' => null,
    'buttonText'  => 'Browse file',
])

@php
    $defaultTitle = $withButton
        ? 'Click the button below to upload'
        : '<span class="font-semibold">Click to upload</span> or drag and drop';

    $defaultDescription = $withButton
        ? 'Max. File Size: <span class="font-semibold">30MB</span>'
        : 'SVG, PNG, JPG or GIF (MAX. 800x400px)';

    $resolvedTitle       = $title       ?? $defaultTitle;
    $resolvedDescription = $description ?? $defaultDescription;
@endphp

<div id="{{ $dropzoneId }}" class="flex items-center justify-center w-full">
    @if (!$withButton)

        <label for="{{ $inputId }}" class="flex flex-col items-center justify-center w-full h-64 bg-neutral-secondary-medium border border-dashed border-default-strong rounded-base cursor-pointer hover:bg-neutral-tertiary-medium">
            <div class="flex flex-col items-center justify-center text-body pt-5 pb-6">
                @isset($icon)
                    {{ $icon }}
                @else
                    <x-fwb-o-upload />
                @endisset
                <p class="mb-2 text-sm">{!! $resolvedTitle !!}</p>
                <p class="text-xs">{!! $resolvedDescription !!}</p>
            </div>
            <input id="{{ $inputId }}" type="file" class="hidden" {{ $attributes }}>
        </label>

    @else

        <div class="flex flex-col items-center justify-center w-full h-64 bg-neutral-secondary-medium border border-dashed border-default-strong rounded-base">
            <div class="flex flex-col items-center justify-center text-body pt-5 pb-6">
                @isset($icon)
                    {{ $icon }}
                @else
                    <x-fwb-o-upload />
                @endisset
                <p class="mb-2 text-sm">{!! $resolvedTitle !!}</p>
                <p class="text-xs mb-4">{!! $resolvedDescription !!}</p>

                <x-kal::button size="sm" class="flex items-center" onclick="document.getElementById('{{ $inputId }}').click()">
                    {{ $buttonText }}
                    <x-fwb-o-search class="w-4 h-4 ms-1.5"/>
                </x-kal::button>
            </div>
        </div>
        <input id="{{ $inputId }}" type="file" class="hidden" {{ $attributes }}>

    @endif
</div>
