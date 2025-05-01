@use(Illuminate\Support\Facades\Route)
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <script src="https://cdn.tailwindcss.com"></script>

        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Figtree', 'ui-sans-serif', 'system-ui', 'sans-serif', "Apple Color Emoji", "Segoe UI Emoji"],
                        }
                    }
                }
            }
        </script>
    </head>

    <body class="antialiased">

        <nav class="bg-white dark:bg-gray-900 fixed w-full z-20 top-0 start-0 border-b border-gray-200 dark:border-gray-600">
            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                <a href="https://flowbite.com/" class="flex items-center space-x-3 rtl:space-x-reverse">
                    <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo">
                    <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Kalion</span>
                </a>

                <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
                    <ul class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                        <li>
                            <a href="{{ safe_route('index', '/') }}"
                               @class(["block py-2 px-3 rounded-sm hover:text-blue-700 md:p-0", "text-blue-700" => Route::currentRouteNamed("home")])
                               aria-current="page"
                            >Home</a>
                        </li>
                        <li>
                            <a href="{{ route("kalion.queues.queuedJobs") }}"
                               @class(["block py-2 px-3 rounded-sm hover:text-blue-700 md:p-0", "text-blue-700" => Route::currentRouteNamed("kalion.queues.queuedJobs")])
                            >Queued Jobs</a>
                        </li>
                        <li>
                            <a href="{{ route("kalion.queues.failedJobs") }}"
                               @class(["block py-2 px-3 rounded-sm hover:text-blue-700 md:p-0", "text-blue-700" => Route::currentRouteNamed("kalion.queues.failedJobs")])
                            >Failed Jobs</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-gray-100 selection:bg-red-500 selection:text-white">
            <div class="w-3/4 mx-auto p-6">
                <div class="px-6 py-4 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex items-center focus:outline focus:outline-2 focus:outline-red-500">

                    <div id="tableJobs"></div>

                </div>
            </div>
        </div>

    </body>
</html>
