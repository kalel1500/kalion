<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Unit;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\DataProvider;
use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\SnapshotDiff;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\DateFormat;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\TimeNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\TimestampNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\TimestampVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\TimeVo;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\PostEntity;
use Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects\ScheduleDto;
use Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\Entities\ScheduleEntity;
use PHPUnit\Framework\TestCase;

class DateVoTest extends TestCase
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
        kalion()->config()->reset();
        date_default_timezone_set($this->originalTimezone);
        parent::tearDown();
    }

    // ---------------------------------------------------------------------------------------------------------------
    // Constructor
    // ---------------------------------------------------------------------------------------------------------------

    public function test_constructor_stores_an_immutable_carbon_in_the_application_timezone(): void
    {
        $vo = new TimestampVo(CarbonImmutable::parse(self::ELOQUENT));

        $this->assertInstanceOf(CarbonImmutable::class, $vo->value);
        $this->assertSame('Europe/Madrid', $vo->value->getTimezone()->getName());
        $this->assertSame('2026-03-29 04:30:00', $vo->toDatetimeYMD());
    }

    public function test_constructor_with_timezone(): void
    {
        $vo = new TimestampVo(CarbonImmutable::parse(self::ELOQUENT), 'America/New_York');

        $this->assertSame('America/New_York', $vo->value->getTimezone()->getName());
        $this->assertSame('2026-03-28 22:30:00', $vo->toDatetimeYMD());
    }

    public function test_constructor_accepts_any_date_time_interface_and_never_mutates_it(): void
    {
        $carbon = Carbon::parse('2026-03-28 22:30:00', 'America/New_York');

        $vo = new TimestampVo($carbon);
        $carbon->addDay();

        $this->assertSame('America/New_York', $carbon->getTimezone()->getName());
        $this->assertSame('2026-03-29 04:30:00', $vo->toDatetimeYMD());
        $this->assertSame('2026-03-29 04:30:00', (new DateVo(new DateTimeImmutable(self::ELOQUENT)))->toDatetimeYMD());
    }

    public function test_constructor_rejects_null_in_required_vos(): void
    {
        $this->assertNull((new TimestampNullVo(null))->value);

        $this->expectException(InvalidValueException::class);
        new TimestampVo(null);
    }

    // ---------------------------------------------------------------------------------------------------------------
    // from()
    // ---------------------------------------------------------------------------------------------------------------

    public function test_from_eloquent_value_inside_the_local_dst_gap(): void
    {
        $vo = TimestampVo::from(self::ELOQUENT);

        $this->assertSame('Europe/Madrid', $vo->value->getTimezone()->getName());
        $this->assertSame('2026-03-29 04:30:00', $vo->toDatetimeYMD());
    }

    public function test_from_local_non_existent_wall_time_is_accepted_and_moved_forward(): void
    {
        // 02:30 does not exist in Europe/Madrid on 2026-03-29: like PHP/Carbon, it is moved forward to 03:30 CEST
        $vo = DateVo::from('2026-03-29 02:30:00');

        $this->assertSame('2026-03-29 03:30:00', $vo->toDatetimeYMD());
        $this->assertSame('2026-03-29T01:30:00+00:00', $vo->utc()->toIso8601String());
    }

    public function test_from_local_ambiguous_wall_time_is_accepted(): void
    {
        $this->assertSame('2026-10-25 02:30:00', DateVo::from('2026-10-25 02:30:00')->toDatetimeYMD());
    }

    public function test_from_mixed_hydration_is_coherent(): void
    {
        $fromEloquent = TimestampVo::from(self::ELOQUENT);
        $fromDb       = TimestampVo::from(self::LOCAL);

        $this->assertSame('Europe/Madrid', $fromEloquent->value->getTimezone()->getName());
        $this->assertSame('Europe/Madrid', $fromDb->value->getTimezone()->getName());
        $this->assertTrue($fromEloquent->value->eq($fromDb->value));

        foreach (['toDatetimeDMYSlash', 'toDatetimeDMYSlashShort', 'toDatetimeYMD', 'toDatetimeYMDShort'] as $method) {
            $this->assertSame($fromDb->{$method}(), $fromEloquent->{$method}(), $method);
        }
        $this->assertSame('29/03/2026 04:30:00', $fromEloquent->toDatetimeDMYSlash());
        $this->assertSame('2026-03-29T04:30:00+02:00', $fromEloquent->format('c'));
        $this->assertSame($fromDb->format('c'), $fromEloquent->format('c'));
    }

    public function test_from_mixed_hydration_with_utc_application_timezone(): void
    {
        date_default_timezone_set('UTC');

        $fromEloquent = TimestampVo::from(self::ELOQUENT);
        $fromDb       = TimestampVo::from('2026-03-29 02:30:00.000000');

        $this->assertSame('UTC', $fromEloquent->value->getTimezone()->getName());
        $this->assertSame('2026-03-29 02:30:00', $fromEloquent->toDatetimeYMD());
        $this->assertSame($fromDb->toDatetimeYMD(), $fromEloquent->toDatetimeYMD());
    }

    public function test_from_with_timezone_interprets_zoneless_strings_in_that_timezone(): void
    {
        // Database in UTC with app.timezone = Europe/Madrid
        $vo = TimestampVo::from('2026-03-29 02:30:00.000000', tz: 'UTC');

        $this->assertSame('UTC', $vo->value->getTimezone()->getName());
        $this->assertTrue($vo->value->eq(CarbonImmutable::parse(self::ELOQUENT)));
        $this->assertSame('2026-03-29 04:30:00', $vo->tz('Europe/Madrid')->toDatetimeYMD());
    }

    public function test_from_with_timezone_converts_strings_with_zulu_designator(): void
    {
        $vo = TimestampVo::from(self::ELOQUENT, tz: 'America/New_York');

        $this->assertSame('America/New_York', $vo->value->getTimezone()->getName());
        $this->assertSame('2026-03-28 22:30:00', $vo->toDatetimeYMD());
    }

    public function test_from_with_custom_formats(): void
    {
        // d/m/Y must never be interpreted as m/d/Y
        $this->assertSame('2026-11-05 10:00:00', DateNullVo::from('05/11/2026 10:00:00', [DateFormat::datetime_DMY_slash->value])->toDatetimeYMD());
        $this->assertSame('2026-11-18 10:00:00', DateNullVo::from('18/11/2026 10:00:00', [DateFormat::datetime_DMY_slash->value])->toDatetimeYMD());
        $this->assertSame('2026-11-18 00:00:00', DateVo::from('2026-11-18', [DateFormat::date_YMD->value])->toDatetimeYMD());
    }

    public function test_from_rejects_values_with_other_formats(): void
    {
        $this->expectException(InvalidValueException::class);
        DateNullVo::from('2026-11-05 10:00:00', [DateFormat::datetime_DMY_slash->value]);
    }

    public function test_from_rejects_values_not_matching_the_default_formats(): void
    {
        $this->expectException(InvalidValueException::class);
        TimestampVo::from('2026-03-29 04:30:00');
    }

    public function test_from_rejects_non_string_values(): void
    {
        $this->expectException(InvalidValueException::class);
        TimestampVo::from(1774751400);
    }

    public function test_from_date_instances_and_null(): void
    {
        $this->assertSame('2026-03-29 04:30:00', TimestampVo::from(CarbonImmutable::parse(self::ELOQUENT))->toDatetimeYMD());
        $this->assertNull(TimestampNullVo::from(null)->value);
        $this->assertNull(TimestampNullVo::tryFrom('not a date'));
    }

    public function test_time_vos(): void
    {
        $this->assertSame('10:15:00', TimeVo::from('10:15:00')->format('H:i:s'));
        $this->assertSame('10:15:00', TimeNullVo::from('10:15:00')->format('H:i:s'));
        $this->assertNull(TimeNullVo::from(null)->value);
    }

    // ---------------------------------------------------------------------------------------------------------------
    // parse()
    // ---------------------------------------------------------------------------------------------------------------

    public function test_parse_accepts_any_format_understood_by_carbon(): void
    {
        $this->assertSame('2026-03-29 04:30:00', TimestampVo::parse('2026-03-29T04:30:00+02:00')->toDatetimeYMD());
        $this->assertSame('2026-03-29 04:30:00', TimestampVo::parse('2026-03-28T22:30:00-04:00')->toDatetimeYMD());
        $this->assertSame('2026-03-29 04:30:00', TimestampVo::parse('2026-03-29 04:30')->toDatetimeYMD());
        $this->assertSame('2026-03-29 04:30:00', TimestampVo::parse(self::ELOQUENT)->toDatetimeYMD());
        $this->assertSame('2026-03-29 00:00:00', DateVo::parse('29 March 2026')->toDatetimeYMD());
        $this->assertSame('Europe/Madrid', TimestampVo::parse(self::ELOQUENT)->value->getTimezone()->getName());
    }

    public function test_parse_with_timezone(): void
    {
        $vo = TimestampVo::parse('2026-03-29 02:30', 'UTC');

        $this->assertSame('UTC', $vo->value->getTimezone()->getName());
        $this->assertTrue($vo->value->eq(CarbonImmutable::parse(self::ELOQUENT)));

        $converted = TimestampVo::parse('2026-03-29T04:30:00+02:00', 'America/New_York');
        $this->assertSame('2026-03-28 22:30:00', $converted->toDatetimeYMD());
    }

    public function test_parse_with_date_instances_and_null(): void
    {
        $this->assertSame('2026-03-29 04:30:00', TimestampVo::parse(new DateTimeImmutable(self::ELOQUENT))->toDatetimeYMD());
        $this->assertNull(TimestampNullVo::parse(null)->value);
    }

    public function test_parse_rejects_invalid_dates(): void
    {
        $this->expectException(InvalidValueException::class);
        TimestampVo::parse('not a date');
    }

    // ---------------------------------------------------------------------------------------------------------------
    // tz() / timezone() / utc() / local()
    // ---------------------------------------------------------------------------------------------------------------

    public function test_tz_returns_a_new_converted_instance(): void
    {
        $vo        = TimestampNullVo::from(self::ELOQUENT);
        $converted = $vo->tz('America/New_York');

        $this->assertNotSame($vo, $converted);
        $this->assertInstanceOf(TimestampNullVo::class, $converted);

        // The original is untouched
        $this->assertSame('Europe/Madrid', $vo->value->getTimezone()->getName());
        $this->assertSame('2026-03-29 04:30:00', $vo->toDatetimeYMD());

        // Conversion (EDT, -04:00) of the same instant
        $this->assertSame('America/New_York', $converted->value->getTimezone()->getName());
        $this->assertSame('28/03/2026 22:30', $converted->toDatetimeDMYSlashShort());
        $this->assertTrue($vo->value->eq($converted->value));
    }

    public function test_timezone_alias_and_date_time_zone_instance(): void
    {
        $vo = TimestampVo::from(self::ELOQUENT);

        $this->assertSame($vo->tz('Asia/Tokyo')->format('c'), $vo->timezone('Asia/Tokyo')->format('c'));
        $this->assertSame('2026-03-29T11:30:00+09:00', $vo->timezone(new DateTimeZone('Asia/Tokyo'))->format('c'));
    }

    public function test_utc(): void
    {
        $utc = TimestampVo::from(self::LOCAL)->utc();

        $this->assertInstanceOf(TimestampVo::class, $utc);
        $this->assertSame('UTC', $utc->value->getTimezone()->getName());
        $this->assertSame('2026-03-29 02:30:00', $utc->toDatetimeYMD());
    }

    public function test_local_uses_the_configured_local_timezone(): void
    {
        date_default_timezone_set('UTC');
        kalion()->config()->fake(['kalion.dates.local_timezone' => 'Europe/Madrid']);

        $vo = TimestampVo::from('2026-03-29 02:30:00.000000');

        $this->assertSame('2026-03-29 02:30:00', $vo->toDatetimeYMD());
        $this->assertSame('29/03/2026 04:30:00', $vo->local()->toDatetimeDMYSlash());
        $this->assertSame('2026-03-29 02:30:00', $vo->local()->utc()->toDatetimeYMD());

        // The application timezone is not affected
        $this->assertSame('UTC', date_default_timezone_get());
    }

    public function test_local_defaults_to_the_application_timezone(): void
    {
        kalion()->config()->fake(['kalion.dates.local_timezone' => null]);

        $local = TimestampVo::from(self::ELOQUENT)->tz('UTC')->local();

        $this->assertSame('Europe/Madrid', $local->value->getTimezone()->getName());
    }

    public function test_timezone_methods_with_null_value(): void
    {
        $vo = TimestampNullVo::from(null);

        $this->assertInstanceOf(TimestampNullVo::class, $vo->tz('UTC'));
        $this->assertNull($vo->tz('UTC')->value);
        $this->assertNull($vo->utc()->value);
        $this->assertNull($vo->local()->toDatetimeYMD());
    }

    // ---------------------------------------------------------------------------------------------------------------
    // toNull() / toNotNull()
    // ---------------------------------------------------------------------------------------------------------------

    public function test_to_null_and_to_not_null_keep_the_timezone(): void
    {
        $vo = DateVo::from('05/11/2026 10:00:00', [DateFormat::datetime_DMY_slash->value])->utc();

        $nullable = $vo->toNull();
        $this->assertInstanceOf(DateNullVo::class, $nullable);
        $this->assertSame('2026-11-05 09:00:00', $nullable->toDatetimeYMD());

        $required = $nullable->toNotNull();
        $this->assertInstanceOf(DateVo::class, $required);
        $this->assertSame('2026-11-05 09:00:00', $required->toDatetimeYMD());
        $this->assertSame('UTC', $required->value->getTimezone()->getName());
    }

    public function test_to_not_null_with_null_value_throws(): void
    {
        $this->expectException(InvalidValueException::class);
        TimestampNullVo::from(null)->toNotNull();
    }

    // ---------------------------------------------------------------------------------------------------------------
    // Carbon methods (__call) and serialization
    // ---------------------------------------------------------------------------------------------------------------

    public function test_carbon_methods_can_be_called_directly(): void
    {
        $vo = TimestampVo::from(self::ELOQUENT);

        $this->assertSame('2026-03-29 04:30:00', $vo->toDateTimeString());
        $this->assertSame('29/03/2026', $vo->format('d/m/Y'));
        $this->assertTrue($vo->isSunday());
        $this->assertTrue($vo->eq(CarbonImmutable::parse(self::ELOQUENT)));

        // Carbon methods return Carbon (the VO is never modified)
        $tomorrow = $vo->addDay();
        $this->assertInstanceOf(CarbonImmutable::class, $tomorrow);
        $this->assertSame('2026-03-30 04:30:00', $tomorrow->toDateTimeString());
        $this->assertSame('2026-03-29 04:30:00', $vo->toDateTimeString());
    }

    public function test_carbon_methods_return_null_when_the_value_is_null(): void
    {
        $vo = TimestampNullVo::from(null);

        $this->assertNull($vo->toDateTimeString());
        $this->assertNull($vo->format('Y-m-d'));
        $this->assertNull($vo->toDatetimeDMYSlash());
    }

    public function test_serialization(): void
    {
        $vo = TimestampVo::from(self::LOCAL);

        $this->assertSame(self::ELOQUENT, $vo->jsonSerialize());
        $this->assertSame('"' . self::ELOQUENT . '"', json_encode($vo));
        $this->assertSame(self::ELOQUENT, (string)$vo);
        $this->assertNull(TimestampNullVo::from(null)->jsonSerialize());
    }

    public function test_serialization_with_custom_format_uses_the_application_timezone(): void
    {
        kalion()->config()->fake(['kalion.dates.json_format' => 'Y-m-d H:i:s']);

        $vo = TimestampVo::from(self::ELOQUENT);

        $this->assertSame('2026-03-29 04:30:00', $vo->jsonSerialize());
        $this->assertSame('"2026-03-29 04:30:00"', json_encode($vo));
        $this->assertNull(TimestampNullVo::from(null)->jsonSerialize());
    }

    public function test_serialization_with_custom_format_with_zulu_designator_uses_utc(): void
    {
        kalion()->config()->fake(['kalion.dates.json_format' => 'Y-m-d H:i:s\Z', 'kalion.dates.json_local' => true, 'kalion.dates.local_timezone' => 'Europe/Madrid']);

        $this->assertSame('2026-03-29 02:30:00Z', TimestampVo::from(self::LOCAL)->jsonSerialize());
    }

    public function test_serialization_local_without_format_returns_iso_with_offset(): void
    {
        kalion()->config()->fake(['kalion.dates.json_local' => true, 'kalion.dates.local_timezone' => 'Europe/Madrid']);

        $this->assertSame('2026-03-29T04:30:00.000000+02:00', TimestampVo::from(self::ELOQUENT)->jsonSerialize());
    }

    public function test_serialization_local_with_custom_format(): void
    {
        kalion()->config()->fake([
            'kalion.dates.json_local'     => true,
            'kalion.dates.local_timezone' => 'Europe/Madrid',
            'kalion.dates.json_format'    => 'd/m/Y H:i:s',
        ]);

        $this->assertSame('29/03/2026 04:30:00', TimestampVo::from(self::ELOQUENT)->jsonSerialize());
    }

    public function test_serialization_local_defaults_to_the_application_timezone(): void
    {
        kalion()->config()->fake(['kalion.dates.json_local' => true, 'kalion.dates.local_timezone' => null, 'kalion.dates.json_format' => 'H:i']);

        $this->assertSame('04:30', TimestampVo::from(self::ELOQUENT)->jsonSerialize());
    }

    public function test_serialization_does_not_depend_on_the_timezone_of_the_vo(): void
    {
        kalion()->config()->fake(['kalion.dates.json_format' => 'H:i']);

        $this->assertSame('04:30', TimestampVo::from(self::ELOQUENT, tz: 'Asia/Tokyo')->jsonSerialize());
    }

    // ---------------------------------------------------------------------------------------------------------------
    // Round trip (from() always accepts the serialization format)
    // ---------------------------------------------------------------------------------------------------------------

    public static function roundTripProvider(): array
    {
        $configs = [
            'default'                 => [],
            'local without format'    => ['kalion.dates.json_local' => true, 'kalion.dates.local_timezone' => 'America/New_York'],
            'custom format'           => ['kalion.dates.json_format' => 'd/m/Y H:i:s'],
            'custom format local'     => ['kalion.dates.json_format' => 'd/m/Y H:i:s', 'kalion.dates.json_local' => true, 'kalion.dates.local_timezone' => 'America/New_York'],
            'custom format with Zulu' => ['kalion.dates.json_format' => 'Y-m-d H:i:s\Z', 'kalion.dates.json_local' => true, 'kalion.dates.local_timezone' => 'America/New_York'],
            'storage format'          => ['kalion.dates.json_format' => 'Y-m-d H:i:s'],
        ];

        $classes = [DateVo::class, DateNullVo::class, TimestampVo::class, TimestampNullVo::class, TimeVo::class, TimeNullVo::class];

        $cases = [];
        foreach ($configs as $configName => $config) {
            foreach ($classes as $class) {
                $cases[class_basename($class) . ' - ' . $configName] = [$class, $config];
            }
        }
        return $cases;
    }

    #[DataProvider('roundTripProvider')]
    public function test_from_accepts_its_own_serialization(string $class, array $config): void
    {
        kalion()->config()->fake($config);

        $vo         = new $class(new DateTimeImmutable(self::ELOQUENT));
        $serialized = $vo->jsonSerialize();
        $rebuilt    = $class::from($serialized);

        $this->assertTrue($rebuilt->eq($vo->value), "$serialized => " . $rebuilt->value?->toIso8601String());
        $this->assertSame('Europe/Madrid', $rebuilt->getTimezone()->getName());
    }

    public function test_from_with_explicit_formats_does_not_accept_the_serialization_format(): void
    {
        $this->expectException(InvalidValueException::class);

        DateVo::from(self::ELOQUENT, [DateFormat::datetime_YMD->value]);
    }

    public function test_from_error_lists_the_serialization_format(): void
    {
        kalion()->config()->fake(['kalion.dates.json_format' => 'd/m/Y H:i:s']);

        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Needle formats: <Y-m-d H:i:s.u, Y-m-d\TH:i:s.u\Z, d/m/Y H:i:s>');

        TimestampVo::from('29-03-2026');
    }

    // ---------------------------------------------------------------------------------------------------------------
    // Storage
    // ---------------------------------------------------------------------------------------------------------------

    public function test_to_db_uses_the_storage_format_and_ignores_the_serialization_config(): void
    {
        kalion()->config()->fake(['kalion.dates.json_format' => 'd/m/Y H:i', 'kalion.dates.json_local' => true, 'kalion.dates.local_timezone' => 'America/New_York']);

        $date = new DateTimeImmutable(self::ELOQUENT);

        $this->assertSame(self::LOCAL, TimestampVo::from($date, tz: 'Asia/Tokyo')->toDb());
        $this->assertSame('2026-03-29 04:30:00', DateVo::from($date)->toDb());
        $this->assertSame('04:30:00', TimeVo::from($date)->toDb());
        $this->assertNull(TimestampNullVo::from(null)->toDb());

        // The storage format is read back by from()
        $this->assertTrue(TimestampVo::from(TimestampVo::from($date)->toDb())->eq($date));
        $this->assertTrue(DateVo::from(DateVo::from($date)->toDb())->eq($date));
    }

    // ---------------------------------------------------------------------------------------------------------------
    // Entities
    // ---------------------------------------------------------------------------------------------------------------

    public function test_entity_hydration_and_round_trip(): void
    {
        $data = [
            'id'         => 1,
            'title'      => 'Title',
            'content'    => 'Content',
            'slug'       => 'title',
            'user_id'    => 1,
            'created_at' => self::ELOQUENT,
        ];

        $post = PostEntity::fromArray($data);
        $this->assertSame('29/03/2026 04:30:00', $post->created_at->toDatetimeDMYSlash());

        // toArray() serializes the dates (jsonSerialize) and can be used to rebuild the entity
        $array = $post->toArray();
        $this->assertSame(self::ELOQUENT, $array['created_at']);
        $this->assertSame(self::ELOQUENT, json_decode(json_encode($post), true)['created_at']);
        $this->assertTrue(PostEntity::fromArray($array)->created_at->eq($post->created_at->value));

        // The serialization config is also applied to toArray(), and the entity can still be rebuilt from it
        kalion()->config()->fake(['kalion.dates.json_format' => 'd/m/Y H:i', 'kalion.dates.json_local' => true, 'kalion.dates.local_timezone' => 'America/New_York']);
        $array = $post->toArray();
        $this->assertSame('28/03/2026 22:30', $array['created_at']);
        $this->assertTrue(PostEntity::fromArray($array)->created_at->eq($post->created_at->value));

        // resolveFromArray() uses parse()
        $resolved = PostEntity::resolveFromArray(array_merge($data, ['created_at' => '2026-03-29T04:30:00+02:00']));
        $this->assertTrue($resolved->created_at->eq($post->created_at->value));
    }

    public function test_entity_to_array_db_uses_the_storage_format_and_both_arrays_rebuild_the_entity(): void
    {
        kalion()->config()->fake(['kalion.dates.json_format' => 'd/m/Y H:i:s', 'kalion.dates.json_local' => true, 'kalion.dates.local_timezone' => 'America/New_York']);

        $entity = ScheduleEntity::fromArray([
            'id'        => 1,
            'starts_at' => self::ELOQUENT,
            'ends_on'   => null,
            'opens_at'  => '04:30:00',
        ]);

        $this->assertSame([
            'id'        => 1,
            'starts_at' => '28/03/2026 22:30:00',
            'ends_on'   => null,
            'opens_at'  => $entity->opens_at->tz('America/New_York')->format('d/m/Y H:i:s'),
        ], $entity->toArray());

        $this->assertSame([
            'id'        => 1,
            'starts_at' => self::LOCAL,
            'ends_on'   => null,
            'opens_at'  => '04:30:00',
        ], $entity->toArrayDb(true));

        foreach ([$entity->toArray(), $entity->toArrayDb(true)] as $array) {
            $rebuilt = ScheduleEntity::fromArray($array);
            $this->assertTrue($rebuilt->starts_at->eq($entity->starts_at->value));
            $this->assertTrue($rebuilt->opens_at->eq($entity->opens_at->value));
            $this->assertNull($rebuilt->ends_on->value);
        }
    }

    public function test_dto_can_be_rebuilt_from_its_own_array(): void
    {
        kalion()->config()->fake(['kalion.dates.json_format' => 'd/m/Y H:i:s', 'kalion.dates.json_local' => true, 'kalion.dates.local_timezone' => 'America/New_York']);

        $dto = ScheduleDto::fromArray([
            'starts_at' => self::ELOQUENT,
            'ends_on'   => '2026-03-30 10:00:00',
            'opens_at'  => '04:30:00',
        ]);

        foreach ([$dto->toArray(), $dto->toMakeArray(), json_decode($dto->toJson(), true)] as $array) {
            $rebuilt = ScheduleDto::fromArray($array);
            $this->assertTrue($rebuilt->starts_at->eq($dto->starts_at->value));
            $this->assertTrue($rebuilt->ends_on->eq($dto->ends_on->value));
            $this->assertTrue($rebuilt->opens_at->eq($dto->opens_at->value));
        }
    }

    public function test_snapshot_diff_compares_dates_as_instants(): void
    {
        $old = ['created_at' => TimestampVo::from(self::ELOQUENT)->value, 'title' => 'a'];
        $new = ['created_at' => TimestampVo::from(self::LOCAL)->utc()->value, 'title' => 'a'];

        $this->assertFalse(SnapshotDiff::between($old, $new)->hasChanges());

        $new['created_at'] = $new['created_at']->addSecond();
        $this->assertSame(['created_at'], SnapshotDiff::between($old, $new)->changedFields());
    }
}

