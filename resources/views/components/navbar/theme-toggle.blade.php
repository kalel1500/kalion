@php($theme = \Thehouseofel\Kalion\Core\Infrastructure\Laravel\Facades\LayoutPreferences::get()->theme)
{{--<x-kal::navbar.item
    id="theme-toggle"
    text="Theme toggle"
    tooltip="Toggle dark mode"
>
    <x-fwb-s-moon id="theme-toggle-dark-icon" @class(['hidden' => $dark_theme]) />
    <x-fwb-s-sun id="theme-toggle-light-icon" @class(['hidden' => !$dark_theme]) />
</x-kal::navbar.item>--}}

<!-- Nav item (dark) -->
<x-kal::navbar.item id="theme-dark" tooltip="{{ __('Switch to light mode') }}" @class(['hidden', 'block!' => $theme->isDark()])>
    <x-fwb-s-moon />
</x-kal::navbar.item>

<!-- Nav item (light) -->
<x-kal::navbar.item id="theme-light" tooltip="{{ __('Switch to system mode') }}" @class(['hidden', 'block!' => $theme->isLight()])>
    <x-fwb-s-sun />
</x-kal::navbar.item>

<!-- Nav item (system) -->
<x-kal::navbar.item id="theme-system" tooltip="{{ __('Switch to dark mode') }}" @class(['hidden', 'block!' => $theme->isSystem()])>
    <x-bi-circle-half />
</x-kal::navbar.item>
