@use(Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Kalion)

<section {{ $attributes->mergeTailwind('bg-white dark:bg-gray-800 p-5 rounded-sm dark:text-gray-400 ' . Kalion::getShadowClasses('border border-gray-200 dark:border-gray-700')) }}>
    {{ $slot }}
</section>
