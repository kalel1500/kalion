@use('Thehouseofel\Kalion\Features\Components\Domain\Support\LayoutMetrics')

@props(['leftSide', 'rightSide'])

<nav class="fixed left-0 right-0 top-0 z-50 border-b border-default bg-neutral-primary-soft px-4 h-(--kal-navbar-height) {{ LayoutMetrics::getShadowClasses('') }}">

    <div class="flex h-full flex-wrap items-center justify-between">
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
