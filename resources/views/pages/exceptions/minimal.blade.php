@use('Thehouseofel\Kalion\Infrastructure\Services\Renderer')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    {{-- Js para cargar el DarkMode --}}
    <script>
        if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
            document.documentElement.classList.add("dark");
            document.documentElement.setAttribute("data-theme", "dark");
        }
    </script>

    <!-- JavaScript y CSS del paquete -->
    {!! Renderer::css() !!}
</head>
<body class="antialiased">
<div class="relative flex items-top justify-center min-h-screen bg-[#f7fafc] dark:bg-gray-900 sm:items-center sm:pt-0">
    <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

        <div class="flex items-center pt-8 sm:justify-center sm:pt-0">
            <div class="px-4 text-lg text-[#a0aec0] border-r border-[#cbd5e0] tracking-wider">
                @yield('code')
            </div>

            <div class="ml-4 text-lg text-[#a0aec0] uppercase tracking-wider">
                @yield('title')
            </div>
        </div>

        <div class="p-4 sm:text-center">
            <div class="text-md text-[#a0aec0] tracking-wider">
                @yield('message')
            </div>
        </div>

    </div>
</div>
</body>
</html>
