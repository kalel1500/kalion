@php /** @var \Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Sidebar\Items\SidebarItemDto $item */ @endphp

@props(['item', 'level'])

@if($item->is_separator)
    <x-kal::sidebar.separator/>
@else
    <x-kal::sidebar.item
        :href="$item->hasDropdown() ? null : $item->getHref()"
        :counter="$item->hasCounter() ? $item->getCounter() : null"
        :level="$level"
        :active="current_route_matches($item->route_name, $item->route_params ?? [])"
        :open="$item->isOpenDropdown()"
    >
        @if(!is_null($item->icon))
            <x-slot:icon>
                <x-kal::render-icon :icon="$item->icon"/>
            </x-slot:icon>
        @endif
        <span class="inline sc:hidden">{{ $item->text }}</span>
        <span class="hidden sc:inline">{{ $item->short_text }}</span>
        @if($item->hasDropdown())
            @php($level++)
            <x-slot:dropdown :id="$item->getCode()">
                @foreach($item->dropdown as $subItem)
                    <x-kal::sidebar.item-auto :item="$subItem" :level="$level"/>
                @endforeach
            </x-slot:dropdown>
        @endif
    </x-kal::sidebar.item>
@endif
