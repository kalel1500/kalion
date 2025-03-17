@use('Thehouseofel\Kalion\Infrastructure\Services\Renderer')
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
        <link rel="icon" href="@viteAsset('resources/images/favicon.ico')">

        <!-- Acceso a las rutas de laravel desde javascipt -->
        @routes

        <!-- Variables CSS de cada vista -->
        @stack('css-variables')

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

        <!-- JavaScript para cargar el DarkMode -->
        <script>
            const theme = document.querySelector('html').getAttribute('color-theme');
            if (theme === 'dark' || (theme === 'system' && window.matchMedia("(prefers-color-scheme: dark)").matches)) {
                document.documentElement.classList.add("dark");
                document.documentElement.setAttribute("data-theme", "dark");
            }
        </script>

        <!-- Estilos de cada vista -->
        @stack('styles')

        <!-- /Fin elementos head -->
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
