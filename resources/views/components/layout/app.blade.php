@use('Thehouseofel\Kalion\Core\Infrastructure\Support\Layout\PackageAssets')
@use('Thehouseofel\Kalion\Features\Components\Infrastructure\Assemblers\LayoutAppAssembler')
@use('Thehouseofel\Kalion\Features\Components\Domain\Support\LayoutMetrics')

@props(['package' => false, 'headTitle' => null, 'navbarTitle' => null, 'flush' => false])

@php($data = LayoutAppAssembler::fromProps($package, $headTitle, $navbarTitle, $flush))

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    @class(['dark' => $data->darkMode, 'sc' => $data->sidebarCollapsed])
    data-theme="{{ $data->dataTheme }}"
    color-theme="{{ $data->colorTheme }}"
    style="--kal-navbar-height: {{ LayoutMetrics::navbarHeight() }}; --kal-main-gap: {{ $data->flush ? '0px' : '20px' }};"
>
    <head>
        <!-- Meta tags -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        {{--@env('local')<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">@endenv--}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Title -->
        <title>{{ $data->headTitle }}</title>

        <!-- Icon -->
        <link rel="icon" type="image/x-icon" href="@viteAsset(config('kalion.layout.favicon_path'))">

        {{-- Js para cargar la clase "dark" cuando el "color-theme" es "system" (por si la carga es lenta y el "js" compilado tarda en cargar) --}}
        <x-kal::js.dark-mode/>

        <!-- JS con las rutas de laravel (ziggy) para tener acceso desde el js -->
        @routes

        <!-- CSS con las variables de la vista actual (si tiene) -->
        @stack('css-variables')
        <!--/Fin CSS -->

        @if($data->isFromPackage)
            <!-- JavaScript y CSS del paquete -->
            {!! PackageAssets::css() !!}
            {!! PackageAssets::js() !!}
        @else
            <!-- JavaScript y CSS compilados -->
            @if(file_exists(resource_path('js/app.ts')))
                @vite(['resources/css/app.css', 'resources/js/app.ts'])
            @else
                @vite(['resources/css/app.css', 'resources/js/app.js'])
            @endif
        @endif

        <!-- CSS con los estilos de la vista actual (si tiene) -->
        @stack('styles')
        <!--/Fin CSS -->
    </head>

    <body class="bg-neutral-secondary-soft antialiased">

        <!-- Navbar -->
        <x-kal::navbar.full :navbar-title="$data->navbarTitle" />
{{--        <x-kal::navbar.full-old/>--}}

        <!-- Sidebar -->
        @if($data->sidebarEnabled)
            <x-kal::sidebar.full/>
        @endif

        <!-- Wrapper -->
        @php($sidebarClasses = $data->sidebarEnabled ? 'md:ml-64 md:sc:ml-20 md:transition-all' : '')
        <div class="h-auto p-4 pt-[calc(var(--kal-navbar-height)+var(--kal-main-gap))]  {{ $sidebarClasses }}">

            <!-- Main -->
            @php($mainClass = config('kalion.layout.show_debug_main_border') ? 'border-2 border-dashed border-gray-300 p-2 dark:border-gray-600' : null)
            <main class="{{ $mainClass }}">

                <!-- Page breadcrumb -->
                {{ $breadcrumb ?? '' }}

                <!-- Page mensajes -->
                <x-kal::messages/>

                <!-- Page content -->
                {{ $slot }}

            </main>

            <!-- Footer -->
            @if(config('kalion.layout.active.footer'))
                <x-kal::footer/>
            @endif
        </div>

    </body>
</html>
