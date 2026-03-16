@use(Thehouseofel\Kalion\Core\Infrastructure\Support\Config\Kalion)

<section {{ $attributes->mergeTailwind('bg-neutral-primary-medium block p-6 border border-default-medium rounded-base text-body ' . Kalion::getShadowClasses('shadow-xs')) }}>
    {{ $slot }}
</section>
