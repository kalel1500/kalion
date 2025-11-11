@use('Thehouseofel\Kalion\Core\Infrastructure\Services\Renderer')
<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    @class(['dark' => $darkMode, 'sc' => $sidebarCollapsed])
    data-theme="{{ $dataTheme }}"
    color-theme="{{ $colorTheme }}"
>
    <head>
        <!-- Meta tags -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        {{--@env('local')<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">@endenv--}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Title -->
        <title>{{ $title }}</title>

        <!-- Icon -->
        <link rel="icon" type="image/x-icon" href="@viteAsset(config('kalion.layout.asset_path_favicon'))">

        {{-- Js para cargar la clase "dark" cuando el "color-theme" es "system" (por si la carga es lenta y el "js" compilado tarda en cargar) --}}
        <x-kal::js.dark-mode/>

        <!-- JS con las rutas de laravel (ziggy) para tener acceso desde el js -->
        @routes

        <!-- CSS con las variables de la vista actual (si tiene) -->
        @stack('css-variables')
        <!--/Fin CSS -->

        @if($isFromPackage)
            <!-- JavaScript y CSS del paquete -->
            {!! Renderer::css() !!}
            {!! Renderer::js() !!}
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

    <body class="bg-gray-50 antialiased dark:bg-gray-900">

        <!-- Navbar -->
        <x-kal::navbar.full/>
{{--        <x-kal::navbar.full-old/>--}}

        <!-- Sidebar -->
        <x-kal::sidebar.full/>

        <!-- Wrapper -->
        <div class="h-auto p-4 pt-20 md:ml-64 md:sc:ml-20 md:transition-all">

            <!-- Main -->
            @php($mainClass = config('kalion.layout.blade_show_main_border') ? 'border-2 border-dashed border-gray-300 p-2 dark:border-gray-600' : null)
            <main class="{{ $mainClass }}">

                <!-- Page breadcrumb -->
                {{ $breadcrumb ?? '' }}

                <!-- Page mensajes -->
                <x-kal::messages/>

                <!-- Page content -->
                {{ $slot }}

            </main>

            <!-- Footer -->
            <x-kal::footer/>
        </div>

    </body>
</html>
