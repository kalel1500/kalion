@use('Thehouseofel\Kalion\Features\Components\Domain\Support\LayoutMetrics')
@props(['header','footer'])

{{-- z-40 -> z-38 (for modal) TODO - revisar numeros --}}
<aside class="fixed left-0 top-0 z-38 h-screen w-64 -translate-x-full border-r border-default bg-neutral-primary-soft transition-transform md:translate-x-0 md:sc:w-20 md:transition-[width] md:duration-300 {{ LayoutMetrics::getShadowClasses('') }}" aria-label="Sidenav" id="drawer-navigation">
    <div class="h-screen mt-(--kal-navbar-height)">
        <div class="scroller h-[calc(100vh-7rem)] overflow-y-auto overflow-x-hidden bg-neutral-primary-soft px-3 py-4 sc:h-full md:sc:px-1">
            {{ $header ?? '' }}
            <ul class="space-y-2">
                {{ $slot }}
            </ul>
        </div>
        {{ $footer ?? '' }}
    </div>
</aside>
