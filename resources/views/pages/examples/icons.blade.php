@php /** @var \Thehouseofel\Kalion\Features\Examples\Domain\Objects\DataObjects\Icons\ViewIconsDto $data */ @endphp
@php /** @var \Thehouseofel\Kalion\Features\Examples\Domain\Objects\DataObjects\Icons\IconDto $icon */ @endphp

<x-kal::layout.app package title="Icons">

    <x-kal::section>

        <div id="icons" class="flex flex-wrap gap-3">
            @foreach($data->icons as $key => $icon)
                <div class="pb-2 pt-6 px-6 border-2 border-gray-200 border-dashed rounded-sm dark:border-gray-700">

                    <x-kal::tooltip id="tooltip-{{ $key }}" data-text-id="{{ $key }}">
                        <span>{{ $data->show_name_short->value ? $icon->name_short : $icon->name }}</span>
                    </x-kal::tooltip>

                    <div class="justify-self-center cursor-pointer" data-tooltip-target="tooltip-{{ $key }}" data-icon-id="{{ $key }}">
                        <x-dynamic-component :component="$icon->name"/>
                    </div>

                    <div id="copied-{{ $key }}" class="flex items-center text-xs invisible">
                        <span>{{ __('Copied') }}</span>
                        <span><x-kal::icon.check class="size-2 text-green-600"></x-kal::icon.check></span>
                    </div>
                </div>
            @endforeach
        </div>

    </x-kal::section>

</x-kal::layout.app>
