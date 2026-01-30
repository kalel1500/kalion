@php($theme = \Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Cookie::readOrNew()->preferences()->theme)
{{--<x-kal::navbar.item
    id="theme-toggle"
    text="Theme toggle"
    tooltip="Toggle dark mode"
>
    <x-kal::icon.moon id="theme-toggle-dark-icon" @class(['hidden' => $dark_theme])/>
    <x-kal::icon.sun id="theme-toggle-light-icon" @class(['hidden' => !$dark_theme])/>
</x-kal::navbar.item>--}}

<x-kal::navbar.item id="theme-dark" tooltip="{{ __('Switch to light mode') }}" @class(['hidden', 'block!' => $theme->isDark()])>
    <x-kal::icon.moon/>
</x-kal::navbar.item>

<x-kal::navbar.item id="theme-light" tooltip="{{ __('Switch to system mode') }}" @class(['hidden', 'block!' => $theme->isLight()])>
    <x-kal::icon.sun/>
</x-kal::navbar.item>

<x-kal::navbar.item id="theme-system" tooltip="{{ __('Switch to dark mode') }}" @class(['hidden', 'block!' => $theme->isSystem()])>
    <x-kal::icon.indeterminate/>
</x-kal::navbar.item>
