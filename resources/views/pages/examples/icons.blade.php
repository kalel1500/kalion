@php /** @var \Thehouseofel\Hexagonal\Domain\Objects\DataObjects\Icons\ViewIconsDo $data */ @endphp
@php /** @var \Thehouseofel\Hexagonal\Domain\Objects\DataObjects\Icons\IconDo $icon */ @endphp

<x-hexagonal::layout.app package title="Icons">

    <x-hexagonal::section>

        <div id="icons" class="flex flex-wrap gap-3">
            @foreach($data->icons() as $key => $icon)
                <div class="pb-2 pt-6 px-6 border-2 border-gray-200 border-dashed rounded dark:border-gray-700">

                    <x-hexagonal::tooltip id="tooltip-{{ $key }}" data-text-id="{{ $key }}">
                        <span>{{ $data->show_name_short()->value() ? $icon->name_short() : $icon->name() }}</span>
                    </x-hexagonal::tooltip>

                    <div class="justify-self-center cursor-pointer" data-tooltip-target="tooltip-{{ $key }}" data-icon-id="{{ $key }}">
                        <x-dynamic-component :component="$icon->name()"/>
                    </div>

                    <div id="copied-{{ $key }}" class="flex items-center text-xs invisible">
                        <span>Copied</span>
                        <span><x-hexagonal::icon.check class="size-2 text-green-600"></x-hexagonal::icon.check></span>
                    </div>
                </div>
            @endforeach
        </div>

    </x-hexagonal::section>

</x-hexagonal::layout.app>
