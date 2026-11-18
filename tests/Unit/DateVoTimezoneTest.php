<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Unit;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use DateTimeZone;
use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Tests\TestCase;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\DateFormat;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\TimestampNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\TimestampVo;

class DateVoTimezoneTest extends TestCase
{
    private const ELOQUENT = '2026-03-29T02:30:00.000000Z'; // Eloquent toArray() (UTC)
    private const LOCAL    = '2026-03-29 04:30:00.000000';  // Same instant in Europe/Madrid (CEST, +02:00)

    private string $originalTimezone;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalTimezone = date_default_timezone_get();
        date_default_timezone_set('Europe/Madrid');
    }

    protected function tearDown(): void
    {
        date_default_timezone_set($this->originalTimezone);
        parent::tearDown();
    }

    // ---------------------------------------------------------------------------------------------------------------
    // DST
    // ---------------------------------------------------------------------------------------------------------------

    public function test_eloquent_value_inside_the_local_dst_gap_is_valid(): void
    {
        $vo = TimestampVo::from(self::ELOQUENT);

        $this->assertSame(self::ELOQUENT, $vo->value);
        $this->assertSame('2026-03-29 04:30:00', $vo->toDatetimeYMD());
    }

    public function test_local_non_existent_wall_time_is_accepted_and_normalized_forward(): void
    {
        // 02:30 does not exist in Europe/Madrid on 2026-03-29: like PHP/Carbon, it is moved forward to 03:30 CEST
        $vo = DateVo::from('2026-03-29 02:30:00');

        $this->assertSame('2026-03-29 02:30:00', $vo->value);
        $this->assertSame('2026-03-29 03:30:00', $vo->toDatetimeYMD());
        $this->assertSame('2026-03-29T01:30:00+00:00', $vo->carbon()->utc()->toIso8601String());
    }

    public function test_local_ambiguous_wall_time_is_accepted(): void
    {
        $vo = DateVo::from('2026-10-25 02:30:00');

        $this->assertSame('2026-10-25 02:30:00', $vo->toDatetimeYMD());
    }

    public function test_invalid_format_throws(): void
    {
        $this->expectException(InvalidValueException::class);
        TimestampVo::from('2026-03-29 04:30:00');
    }

    // ---------------------------------------------------------------------------------------------------------------
    // Mixed hydration
    // ---------------------------------------------------------------------------------------------------------------

    public function test_mixed_hydration_is_coherent_in_the_application_timezone(): void
    {
        $fromEloquent = TimestampVo::from(self::ELOQUENT);
        $fromDb       = TimestampVo::from(self::LOCAL);

        // The canonical value is kept as received
        $this->assertSame(self::ELOQUENT, $fromEloquent->value);
        $this->assertSame(self::LOCAL, $fromDb->value);

        // Both live in the application timezone
        $this->assertSame('Europe/Madrid', $fromEloquent->carbon()->getTimezone()->getName());
        $this->assertSame('Europe/Madrid', $fromDb->carbon()->getTimezone()->getName());
        $this->assertTrue($fromEloquent->carbon()->eq($fromDb->carbon()));

        // And therefore every presentation method returns the same result
        foreach (['toDatetimeDMYSlash', 'toDatetimeDMYSlashShort', 'toDatetimeYMD', 'toDatetimeYMDShort'] as $method) {
            $this->assertSame($fromDb->{$method}(), $fromEloquent->{$method}(), $method);
        }
        $this->assertSame('29/03/2026 04:30:00', $fromEloquent->toDatetimeDMYSlash());
        $this->assertSame($fromDb->format('c'), $fromEloquent->format('c'));
        $this->assertSame('2026-03-29T04:30:00+02:00', $fromEloquent->format('c'));
    }

    public function test_mixed_hydration_with_utc_application_timezone(): void
    {
        date_default_timezone_set('UTC');

        $fromEloquent = TimestampVo::from(self::ELOQUENT);
        $fromDb       = TimestampVo::from('2026-03-29 02:30:00.000000');

        $this->assertSame('UTC', $fromEloquent->carbon()->getTimezone()->getName());
        $this->assertSame('2026-03-29 02:30:00', $fromEloquent->toDatetimeYMD());
        $this->assertSame($fromDb->toDatetimeYMD(), $fromEloquent->toDatetimeYMD());
    }

    public function test_custom_instance_formats_are_used_for_validation_and_parsing(): void
    {
        // d/m/Y must never be interpreted as m/d/Y
        $vo = DateNullVo::from('05/11/2026 10:00:00', [DateFormat::datetime_DMY_slash->value]);

        $this->assertSame('2026-11-05 10:00:00', $vo->toDatetimeYMD());

        $date = DateVo::from('2026-11-18', [DateFormat::date_YMD->value]);
        $this->assertSame('2026-11-18 00:00:00', $date->toDatetimeYMD());
    }

    public function test_custom_instance_formats_reject_other_formats(): void
    {
        $this->expectException(InvalidValueException::class);
        DateNullVo::from('2026-11-05 10:00:00', [DateFormat::datetime_DMY_slash->value]);
    }

    // ---------------------------------------------------------------------------------------------------------------
    // tz() / timezone()
    // ---------------------------------------------------------------------------------------------------------------

    public function test_tz_returns_a_new_projected_instance(): void
    {
        $vo        = TimestampNullVo::from(self::ELOQUENT);
        $projected = $vo->tz('America/New_York');

        $this->assertNotSame($vo, $projected);
        $this->assertInstanceOf(TimestampNullVo::class, $projected);

        // Original is untouched
        $this->assertSame('Europe/Madrid', $vo->carbon()->getTimezone()->getName());
        $this->assertSame('2026-03-29 04:30:00', $vo->toDatetimeYMD());

        // Projection (EDT, -04:00)
        $this->assertSame('America/New_York', $projected->carbon()->getTimezone()->getName());
        $this->assertSame('2026-03-28 22:30:00', $projected->toDatetimeYMD());
        $this->assertSame('28/03/2026 22:30', $projected->toDatetimeDMYSlashShort());
        $this->assertTrue($vo->carbon()->eq($projected->carbon()));

        // The canonical value is kept, so it still represents the same instant
        $this->assertSame(self::ELOQUENT, $projected->value);
        $this->assertSame(self::ELOQUENT, $projected->jsonSerialize());
        $this->assertTrue(TimestampVo::from($projected->value)->carbon()->eq($projected->carbon()));
    }

    public function test_tz_keeps_the_value_of_zoneless_strings(): void
    {
        $vo        = TimestampVo::from(self::LOCAL);
        $projected = $vo->tz('UTC');

        $this->assertSame(self::LOCAL, $projected->value);
        $this->assertSame('2026-03-29 02:30:00', $projected->toDatetimeYMD());
        $this->assertTrue(TimestampVo::from($projected->value)->carbon()->eq($projected->carbon()));
    }

    public function test_timezone_alias_and_date_time_zone_instance(): void
    {
        $vo = TimestampVo::from(self::ELOQUENT);

        $this->assertSame($vo->tz('Asia/Tokyo')->format('c'), $vo->timezone('Asia/Tokyo')->format('c'));
        $this->assertSame('2026-03-29T11:30:00+09:00', $vo->timezone(new DateTimeZone('Asia/Tokyo'))->format('c'));
    }

    public function test_tz_with_null_value(): void
    {
        $projected = TimestampNullVo::from(null)->tz('UTC');

        $this->assertInstanceOf(TimestampNullVo::class, $projected);
        $this->assertNull($projected->carbon());
        $this->assertNull($projected->toDatetimeYMD());
    }

    public function test_to_null_and_to_not_null_keep_formats_and_projection(): void
    {
        $vo = DateVo::from('05/11/2026 10:00:00', [DateFormat::datetime_DMY_slash->value])->tz('UTC');

        $nullable = $vo->toNull();
        $this->assertInstanceOf(DateNullVo::class, $nullable);
        $this->assertSame('2026-11-05 09:00:00', $nullable->toDatetimeYMD());

        $required = $nullable->toNotNull();
        $this->assertInstanceOf(DateVo::class, $required);
        $this->assertSame('2026-11-05 09:00:00', $required->toDatetimeYMD());
    }

    // ---------------------------------------------------------------------------------------------------------------
    // fromCarbon() / parse()
    // ---------------------------------------------------------------------------------------------------------------

    public function test_from_carbon_preserves_the_instant_for_every_format(): void
    {
        $newYork = CarbonImmutable::parse('2026-03-28 22:30:00', 'America/New_York');

        // Zoneless default format => application timezone
        $local = TimestampVo::fromCarbon($newYork);
        $this->assertSame(self::LOCAL, $local->value);

        // \Z format => UTC (no fake Z with local time)
        $utc = TimestampVo::fromCarbon($newYork, DateFormat::datetime_eloquent->value);
        $this->assertSame(self::ELOQUENT, $utc->value);

        $this->assertTrue($local->carbon()->eq($newYork));
        $this->assertTrue($utc->carbon()->eq($newYork));
    }

    public function test_from_carbon_with_local_carbon_and_zulu_format(): void
    {
        $madrid = CarbonImmutable::parse('2026-03-29 04:30:00');

        $this->assertSame(self::ELOQUENT, TimestampVo::fromCarbon($madrid, DateFormat::datetime_eloquent->value)->value);
    }

    public function test_from_carbon_does_not_mutate_mutable_carbon(): void
    {
        $carbon = Carbon::parse('2026-03-28 22:30:00', 'America/New_York');

        TimestampVo::fromCarbon($carbon, DateFormat::datetime_eloquent->value);

        $this->assertSame('America/New_York', $carbon->getTimezone()->getName());
        $this->assertSame('2026-03-28 22:30:00', $carbon->format('Y-m-d H:i:s'));
    }

    public function test_from_carbon_and_parse_with_null(): void
    {
        $this->assertNull(TimestampNullVo::fromCarbon(null)->value);
        $this->assertNull(TimestampNullVo::parse(null)->value);
    }

    public function test_parse_with_strings_and_date_instances(): void
    {
        // String with offset
        $this->assertSame(self::ELOQUENT, TimestampVo::parse('2026-03-29T04:30:00+02:00', DateFormat::datetime_eloquent->value)->value);
        $this->assertSame(self::LOCAL, TimestampVo::parse('2026-03-28T22:30:00-04:00')->value);

        // Zoneless string => application timezone
        $this->assertSame(self::ELOQUENT, TimestampVo::parse('2026-03-29 04:30:00', DateFormat::datetime_eloquent->value)->value);

        // Native DateTimeInterface and Carbon
        $this->assertSame(self::LOCAL, TimestampVo::parse(new DateTimeImmutable(self::ELOQUENT))->value);
        $this->assertSame('2026-03-29 04:30:00', DateVo::parse(CarbonImmutable::parse(self::ELOQUENT))->value);
    }

    public function test_parse_keeps_custom_formats(): void
    {
        $vo = DateVo::parse('2026-11-05 10:00:00', DateFormat::datetime_DMY_slash->value, [DateFormat::datetime_DMY_slash->value]);

        $this->assertSame('05/11/2026 10:00:00', $vo->value);
        $this->assertSame('2026-11-05 10:00:00', $vo->toDatetimeYMD());
    }
}

