@props(['href' => '#', 'icon', 'dropdown', 'counter', 'level' => 0, 'active' => false])

@php
    $isDropdown = isset($dropdown);
    $hasIcon = isset($icon);
    $isSubitem = $attributes->has('subitem');
    $centerClass = !$hasIcon ? 'sc:items-start' : '';
    $linkClasses = 'group flex w-full items-center '.$centerClass.' rounded-lg p-2 text-base font-medium text-heading transition duration-75 hover:bg-neutral-tertiary-medium md:sc:p-1 md:sc:flex-col md:sc:text-xs md:sc:font-normal md:transition-all ';
    $iconHtml = !$hasIcon ? '' : '<div class="h-6 w-6 shrink-0 text-body transition duration-75 group-hover:text-heading">' . $icon . '</div>';
    $spanClasses = !$hasIcon ? '' : 'ml-3 md:sc:ml-0 truncate md:sc:w-full';
    $dropdownId = $isDropdown ? $dropdown->attributes->get('id') : '';
    $dropdownIsOpen = $isDropdown && dropdown_is_open($dropdown->toHtml());
    $isDeepLevel = (int)$level > 0;

    /*
     * Código interesante por si queremos sobreescribir las clases del svg en lugar de envolverlo con un div
    // Obtén el HTML del SVG desde $icon
    $iconHtml = $icon->toHtml(); // Asegúrate de que $icon puede ser convertido a HTML.
    // Reemplaza la clase del SVG
    $modifiedIconHtml = preg_replace('/class="([^"]*)"/', 'class="h-6 w-6 shrink-0 transition duration-75 group-hover:text-heading"',$iconHtml);
    */
@endphp

<li data-level="{{ $level }}" @class(['sc:hidden' => $isDropdown && $isDeepLevel])>
    @if($isDropdown)
        <button type="button" class="{{ $linkClasses }}" aria-controls="dropdown-{{ $dropdownId }}" data-collapse-toggle="dropdown-{{ $dropdownId }}">
            {!! $iconHtml !!}
            <span @class([$spanClasses, 'flex-1 text-left sc:text-center'])>{{ $slot }}</span>
            <x-heroicon-o-chevron-down stroke-width="3.5" @class(['size-4', 'chevron-down', '-rotate-90' => $dropdownIsOpen]) />
        </button>

        <ul id="dropdown-{{ $dropdownId }}" @class(['space-y-2 py-2 ml-4 sc:ml-3 sc:space-y-0 sc:pt-0', 'hidden' => !$dropdownIsOpen])>
            {{ $dropdown }}
        </ul>
    @else
        <a href="{{ $href }}" @class([$linkClasses, 'bg-neutral-tertiary-medium' => $active, 'pl-11' => $isSubitem])>
            @if ($isSubitem)
                {{ $slot }}
            @else
                {!! $iconHtml !!}
                <span @class([$spanClasses, 'flex-1 whitespace-nowrap' => isset($counter), 'text-center' => (!isset($counter) && $level === '0')])>{{ $slot }}</span>
                @isset($counter)
                    <span class="text-blue-800 bg-blue-100 dark:bg-blue-200 dark:text-blue-800 inline-flex h-5 w-5 items-center justify-center rounded-full text-xs font-semibold md:sc:hidden">{{ $counter }}</span>
                @endisset
            @endif
        </a>
    @endif
</li>
