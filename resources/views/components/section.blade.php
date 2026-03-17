@use('Thehouseofel\Kalion\Features\Components\Domain\Support\LayoutMetrics')

<section {{ $attributes->twMerge('bg-neutral-primary-medium block p-6 border border-default-medium rounded-base text-body ' . LayoutMetrics::getShadowClasses('shadow-xs')) }}>
    {{ $slot }}
</section>
