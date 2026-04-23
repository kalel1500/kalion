@use('Thehouseofel\Kalion\Features\Components\Domain\Support\LayoutMetrics')

@props(['leftSide', 'rightSide'])

{{-- z-50 -> z-39 (for modal) TODO - revisar numeros --}}
<nav class="fixed left-0 right-0 top-0 z-39 border-b border-default bg-neutral-primary-soft px-4 h-(--kal-navbar-height) {{ LayoutMetrics::getShadowClasses('') }}">

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
