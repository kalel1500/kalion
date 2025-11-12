@use('Thehouseofel\Kalion\Features\Components\Infrastructure\Services\NavbarFullAssembler')

@php /** @var \Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Layout\NavbarItemDto $item */ @endphp
@php /** @var \Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Layout\NavbarItemDto $subItem */ @endphp

@php($data = NavbarFullAssembler::fromProps())

        <!-- New -->
<x-kal::navbar>
    <x-slot:left-side>
        <x-kal::navbar.hamburger-icon/>

        <x-kal::navbar.brand/>

        @if($data->showSearch)
            <x-kal::navbar.search-form/>
        @endif
    </x-slot:left-side>

    <x-slot:right-side>
        @if($data->showSearch)
            <!-- Toggle sidebar mobile search -->
            <x-kal::navbar.search-toggle-mobile/>
        @endif

        @foreach($data->items as $item)
            @if($item->is_theme_toggle)
                <!-- Theme toggle -->
                <x-kal::navbar.theme-toggle/>
            @else
                <!-- {{ $item->getCode() }} -->
                <x-kal::navbar.item :id="$item->getCode()" :text="$item->text" :user="$item->is_user">
                    <!-- Item icon -->
                    @if($item->is_user)
                        <x-kal::icon.user-profile class="h-8 w-8 hover:bg-gray-700 dark:hover:bg-gray-300"/>
                    @else
                        <x-kal::render-icon :icon="$item->icon"/>
                    @endif

                    @if($item->hasDropdown())
                        <x-slot:dropdown>
                            <x-kal::navbar.dropdown
                                    :big-list="$item->dropdown->is_list"
                                    :big-square="$item->dropdown->is_square"
                                    :header="$item->dropdown->header"
                            >
                                @if($item->is_user)
                                    <x-slot:header href="#">
                                        <x-kal::navbar.dropdown.user-info :id="$item->dropdown->userInfo?->id" :name="$item->dropdown->userInfo?->name" :email="$item->dropdown->userInfo?->email"/>
                                    </x-slot:header>
                                @endif

                                @foreach($item->dropdown->items as $subItem)
                                    @if($item->dropdown->is_list)
                                        <x-kal::navbar.dropdown.link :href="$subItem->getHref()" :time="$subItem->time">
                                            <x-slot:icon>
                                                <x-kal::render-icon :icon="$subItem->icon" class="h-11 w-11"/>
                                            </x-slot:icon>
                                            {{ $subItem->text }}
                                        </x-kal::navbar.dropdown.link>
                                    @elseif($item->dropdown->is_square)
                                        <x-kal::navbar.dropdown.link :href="$subItem->getHref()" :text="$subItem->text">
                                            <x-kal::render-icon :icon="$subItem->icon"/>
                                        </x-kal::navbar.dropdown.link>
                                    @elseif($subItem->is_separator)
                                        <x-kal::navbar.dropdown.separator/>
                                    @else
                                        <x-kal::navbar.dropdown.link :href="$subItem->getHref()" :is_post="$subItem->is_post">
                                            @if($subItem->icon)
                                                <x-kal::render-icon :icon="$subItem->icon" class="size-5 mr-2"/>
                                            @endif
                                            {{ $subItem->text }}
                                        </x-kal::navbar.dropdown.link>
                                    @endif
                                @endforeach

                                @if($item->dropdown->footer)
                                    <x-slot:footer :href="$item->dropdown->footer->getHref()">
                                        <x-kal::render-icon :icon="$item->dropdown->footer->icon"/>
                                        {{ $item->dropdown->footer->text }}
                                    </x-slot:footer>
                                @endif
                            </x-kal::navbar.dropdown>

                        </x-slot:dropdown>
                    @endif
                </x-kal::navbar.item>
            @endif
        @endforeach

    </x-slot:right-side>
</x-kal::navbar>
