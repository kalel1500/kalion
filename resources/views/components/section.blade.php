@use('Thehouseofel\Kalion\Features\Components\Domain\Support\LayoutMetrics')

<section {{ $attributes->twMerge('bg-neutral-primary-soft block p-6 border border-default rounded-base text-body ' . LayoutMetrics::getShadowClasses('shadow-xs')) }}>
    {{ $slot }}
</section>
