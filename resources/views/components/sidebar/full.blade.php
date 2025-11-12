@use('Thehouseofel\Kalion\Features\Components\Infrastructure\Assemblers\SidebarFullAssembler')

@php /** @var \Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Layout\SidebarItemDto $item */ @endphp
@php /** @var \Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Layout\SidebarItemDto $subItem */ @endphp

@php($data = SidebarFullAssembler::fromProps())

<x-kal::sidebar>
    @if($data->showSearch)
        <x-slot:header>
            <x-kal::sidebar.search-from :action="$data->searchAction"/>
        </x-slot:header>
    @endif

    @foreach($data->items as $item)
        <x-kal::sidebar.item-auto :item="$item" level="0"/>
    @endforeach

    @if($data->hasFooter)
        <x-slot:footer>
            <x-kal::sidebar.footer>
                @foreach($data->footer as $item)
                    <x-kal::sidebar.footer.item
                            :href="$item->hasDropdown() ? null : $item->getHref()"
                            :id="$item->code ?? null"
                            :tooltip="$item->tooltip ?? null"
                    >
                        <x-kal::render-icon :icon="$item->icon"/>
                        @if($item->hasDropdown())
                            <x-slot:dropdown>
                                @foreach($item->dropdown as $subItem)
                                    <x-kal::sidebar.footer.subitem>
                                        <x-kal::render-icon :icon="$subItem->icon"/>
                                        {{ $subItem->text }}
                                    </x-kal::sidebar.footer.subitem>
                                @endforeach
                            </x-slot:dropdown>
                        @endif
                    </x-kal::sidebar.footer.item>
                @endforeach
            </x-kal::sidebar.footer>
        </x-slot:footer>
    @endif
</x-kal::sidebar>
