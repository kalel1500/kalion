<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Feature;

use ReflectionClass;
use Thehouseofel\Kalion\Core\Domain\Exceptions\UnauthorizedException;
use Thehouseofel\Kalion\Core\Domain\KalionKernel;
use Thehouseofel\Kalion\Core\Domain\Support\KalionConfigReader;
use Thehouseofel\Kalion\Core\Infrastructure\Kalion;
use Thehouseofel\Kalion\Tests\TestCase;

class KalionKernelWithLaravelTest extends TestCase
{
    public function test_kalion_helper_resolves_the_scoped_kalion_instance_from_the_container(): void
    {
        $this->assertInstanceOf(Kalion::class, kalion());
        $this->assertSame(kalion(), kalion());
        $this->assertSame(app(Kalion::class), kalion());
        $this->assertSame(app(KalionKernel::class), kalion());

        $before = kalion();
        $this->app->forgetScopedInstances();

        $this->assertNotSame($before, kalion());
    }

    public function test_config_reads_the_laravel_config(): void
    {
        config()->set('kalion.minimum_value_for_id', 7);
        config()->set('kalion.auth.display_role_in_exception', true);
        config()->set('kalion.entity_calculated_props_mode', 'f');

        $this->assertSame(7, kalion()->config()->minimumValueForId());
        $this->assertTrue(kalion()->config()->displayRoleInException());
        $this->assertSame('f', kalion()->config()->entityCalculatedPropsMode());
        $this->assertSame(config('app.timezone'), kalion()->config()->get('app.timezone'));
    }

    public function test_package_config_values_match_the_reader_defaults(): void
    {
        $defaults = (new ReflectionClass(KalionConfigReader::class))->getConstant('DEFAULTS');

        foreach ($defaults as $method => [$key, $default]) {
            if (! str_starts_with($key, 'kalion.')) {
                continue;
            }

            $this->assertSame($default, kalion()->config()->{$method}(), "config('$key') does not return the default value.");
        }
    }

    public function test_fake_takes_precedence_over_the_laravel_config(): void
    {
        config()->set('kalion.minimum_value_for_id', 7);

        kalion()->config()->fake(['kalion.minimum_value_for_id' => 3]);
        $this->assertSame(3, kalion()->config()->minimumValueForId());

        kalion()->config()->reset();
        $this->assertSame(7, kalion()->config()->minimumValueForId());
    }

    public function test_environment_and_debug_follow_the_laravel_config(): void
    {
        $this->assertSame(config('app.env'), kalion()->env()->value);

        config()->set('app.env', 'testing');
        $this->assertTrue(kalion()->isTesting());

        config()->set('app.env', 'local');
        $this->assertTrue(kalion()->isLocal());

        config()->set('app.debug', true);
        $this->assertTrue(kalion()->isDebug());

        config()->set('app.debug', false);
        $this->assertFalse(kalion()->isDebug());
    }

    public function test_trans_uses_the_laravel_translator(): void
    {
        app()->setLocale('en');

        $this->assertSame(__('k::auth.invalid_roles'), kalion()->trans('k::auth.invalid_roles'));
        $this->assertNotSame('k::auth.invalid_roles', kalion()->trans('k::auth.invalid_roles'));
        $this->assertSame(
            __('k::error.amount_must_be_greater_than_number', ['number' => 0]),
            kalion()->trans('k::error.amount_must_be_greater_than_number', ['number' => 0])
        );
    }

    public function test_domain_exceptions_are_translated_and_use_the_laravel_config(): void
    {
        app()->setLocale('en');
        config()->set('kalion.auth.display_role_in_exception', true);

        $this->assertSame(
            __('k::auth.invalid_roles') . ' ' . __('k::auth.necessary_roles', ['roles' => 'admin']),
            UnauthorizedException::forRoles('admin')->getMessage()
        );
    }
}
