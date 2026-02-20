@use(Thehouseofel\Kalion\Core\Infrastructure\Support\Config\Kalion)
@props(['leftSide', 'rightSide'])

<nav class="fixed left-0 right-0 top-0 z-50 border-b border-gray-200 bg-white px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 {{ Kalion::getShadowClasses('') }}">
    <div class="flex flex-wrap items-center justify-between">
        <!-- Left side -->
        <div class="flex items-center justify-start">
            {{ $leftSide }}
        </div>

        <!-- Right side -->
        <div class="flex items-center lg:order-2">
            {{ $rightSide }}
        </div>
    </div>
</nav>
