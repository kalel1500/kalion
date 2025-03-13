@php /** @var \Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\SidebarItemDo $item */ @endphp
@use(Illuminate\Support\Facades\Route)

@props(['item', 'level'])

@if($item->is_separator)
    <x-kal::sidebar.separator/>
@else
    <x-kal::sidebar.item
            :href="$item->hasDropdown() ? null : $item->getHref()"
            :counter="$item->hasCounter() ? $item->getCounter() : null"
            :level="$level"
            :active="Route::currentRouteNamed($item->route_name)"
    >
        @if(!is_null($item->icon))
            <x-slot:icon>
                <x-kal::render-icon :icon="$item->icon" />
            </x-slot:icon>
        @endif
        {{ $item->text }}
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
