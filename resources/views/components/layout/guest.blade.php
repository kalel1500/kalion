@use('Thehouseofel\Kalion\Infrastructure\Services\Renderer')
@props(['title', 'cardTitle' => '', 'cardText' => ''])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Title -->
        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        <x-kal::js.dark-mode/>

        <!-- CSS del paquete -->
        {!! Renderer::css() !!}
    </head>
    <body class="bg-gray-50 dark:bg-gray-900">

        <section >
            <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
                <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                    <img class="w-8 h-8 mr-2" src="@viteAsset({{ config('kalion.layout.asset_path_logo') }})" alt="logo">
                    {{ config('app.name', 'Laravel 12') }}
                </a>
                <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                        <div>
                            <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                                {{ $cardTitle }}
                            </h1>
                            <p class="mt-1 font-light text-gray-500 dark:text-gray-400">{{ $cardText }}</p>
                        </div>

                        {{ $slot }}

                    </div>
                </div>
            </div>
        </section>

    </body>
</html>
