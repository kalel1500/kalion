<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Unit;

use BadMethodCallException;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\UnauthorizedException;
use Thehouseofel\Kalion\Core\Domain\KalionKernel;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Core\Domain\Support\KalionConfigReader;
use Thehouseofel\Kalion\Core\Domain\Support\Random;
use Thehouseofel\Kalion\Core\Infrastructure\Kalion;

/**
 * The domain must work without booting Laravel: these tests use plain PHPUnit and an empty container.
 */
class KalionKernelWithoutLaravelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Container::setInstance(null);
        KalionKernel::flushState();
    }

    protected function tearDown(): void
    {
        KalionKernel::flushState();
        Container::setInstance(null);
        parent::tearDown();
    }

    public function test_kalion_helper_returns_the_context_without_laravel(): void
    {
        $this->assertSame(KalionKernel::class, get_class(kalion()));
        $this->assertNotInstanceOf(Kalion::class, kalion());
        $this->assertSame(kalion(), kalion());
    }

    public function test_config_returns_the_defaults_without_laravel(): void
    {
        $config = kalion()->config();

        $this->assertSame('production', $config->appEnv());
        $this->assertFalse($config->appDebug());
        $this->assertTrue($config->authEnabled());
        $this->assertTrue($config->abilitiesEnabled());
        $this->assertSame(1, $config->minimumValueForId());
        $this->assertFalse($config->displayRoleInException());
        $this->assertFalse($config->displayPermissionInException());
        $this->assertFalse($config->exceptionsShowLogoutForm());
        $this->assertSame('s', $config->entityCalculatedPropsMode());
    }

    public function test_get_returns_the_given_default_for_unknown_keys(): void
    {
        $this->assertNull(kalion()->config()->get('kalion.unknown'));
        $this->assertSame('x', kalion()->config()->get('kalion.unknown', 'x'));
    }

    public function test_fake_overrides_values_and_reset_restores_the_defaults(): void
    {
        $config = kalion()->config()->fake([
            'kalion.minimum_value_for_id'     => 0,
            'kalion.auth.abilities_enabled'   => false,
        ]);
        $config->fake(['kalion.entity_calculated_props_mode' => 'f']);

        $this->assertSame(0, $config->minimumValueForId());
        $this->assertFalse($config->abilitiesEnabled());
        $this->assertSame('f', $config->entityCalculatedPropsMode());

        $config->reset();

        $this->assertSame(1, $config->minimumValueForId());
        $this->assertTrue($config->abilitiesEnabled());
        $this->assertSame('s', $config->entityCalculatedPropsMode());
    }

    public function test_values_are_cast_to_the_type_of_the_default(): void
    {
        $config = kalion()->config()->fake([
            'kalion.minimum_value_for_id' => '5',
            'kalion.auth.enabled'         => 0,
        ]);

        $this->assertSame(5, $config->minimumValueForId());
        $this->assertFalse($config->authEnabled());
    }

    public function test_unknown_config_method_throws(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(KalionConfigReader::class . '::unknownSetting() does not exist.');

        kalion()->config()->unknownSetting();
    }

    public function test_flush_state_forgets_the_instance_and_the_faked_values(): void
    {
        $before = kalion();
        $before->config()->fake(['kalion.minimum_value_for_id' => 0]);

        KalionKernel::flushState();

        $this->assertNotSame($before, kalion());
        $this->assertSame(1, kalion()->config()->minimumValueForId());
    }

    public function test_environment_follows_the_config_and_is_not_cached(): void
    {
        $this->assertTrue(kalion()->isProd());

        kalion()->config()->fake(['app.env' => 'testing']);
        $this->assertTrue(kalion()->isTesting());
        $this->assertFalse(kalion()->isProd());

        kalion()->config()->fake(['app.env' => 'local']);
        $this->assertTrue(kalion()->isLocal());

        kalion()->config()->fake(['app.env' => 'preproduction']);
        $this->assertTrue(kalion()->isPre());
    }

    public function test_is_debug_follows_the_config(): void
    {
        $this->assertFalse(kalion()->isDebug());
        $this->assertFalse(debug_enabled());

        kalion()->config()->fake(['app.debug' => true]);

        $this->assertTrue(kalion()->isDebug());
        $this->assertTrue(debug_enabled());
    }

    public function test_trans_returns_the_key_with_the_replacements_without_laravel(): void
    {
        $this->assertNull(kalion()->trans(null));
        $this->assertSame('k::error.feature_unavailable', kalion()->trans('k::error.feature_unavailable'));
        $this->assertSame(
            'Hello john, John, JOHN (:missing)',
            kalion()->trans('Hello :name, :Name, :NAME (:missing)', ['name' => 'john'])
        );
        $this->assertSame(
            'max 10 / maximum 20',
            kalion()->trans('max :max / maximum :maximum', ['max' => 10, 'maximum' => 20])
        );
    }

    public function test_infrastructure_methods_throw_a_descriptive_exception_without_laravel(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('The "kalion()->renderCss()" method requires Laravel');

        kalion()->renderCss();
    }

    public function test_unknown_methods_throw_a_method_not_found_exception(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(KalionKernel::class . '::doesNotExist() does not exist.');

        kalion()->doesNotExist();
    }

    public function test_domain_classes_use_the_config_without_laravel(): void
    {
        try {
            new IdVo(0);
            $this->fail('IdVo(0) should not be valid with the default minimum_value_for_id (1)');
        } catch (InvalidValueException) {
        }

        kalion()->config()->fake(['kalion.minimum_value_for_id' => 0]);

        $this->assertSame(0, (new IdVo(0))->value);
    }

    public function test_domain_exceptions_use_config_and_trans_without_laravel(): void
    {
        $this->assertSame('k::auth.invalid_roles', UnauthorizedException::forRoles('admin')->getMessage());

        kalion()->config()->fake(['kalion.auth.display_role_in_exception' => true]);

        $this->assertSame(
            'k::auth.invalid_roles k::auth.necessary_roles',
            UnauthorizedException::forRoles('admin')->getMessage()
        );
    }

    public function test_domain_support_classes_translate_without_laravel(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('k::error.percentage_must_be_between_0_and_100');

        Random::chance(150);
    }
}
