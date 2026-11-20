<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Unit;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\DateFormat;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Date\DateHelper;

class DateHelperTest extends TestCase
{
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

    public static function utcFormatsProvider(): array
    {
        return [
            'eloquent'               => [DateFormat::datetime_eloquent->value, true],
            'zulu in the middle'     => ['Y-m-d\TH:i:s\Z (\U\T\C)', true],
            'plain datetime'         => [DateFormat::datetime_YMD->value, false],
            'unescaped Z (offset s)' => ['Y-m-d H:i:sZ', false],
            'escaped backslash + Z'  => ['Y-m-d\\\\Z', false],
            'offset P'               => ['Y-m-d\TH:i:sP', false],
        ];
    }

    #[DataProvider('utcFormatsProvider')]
    public function test_timezone_for_format(string $format, bool $isUtc): void
    {
        $this->assertSame($isUtc ? 'UTC' : 'Europe/Madrid', DateHelper::timezoneForFormat($format));
        $this->assertSame($isUtc ? 'UTC' : 'America/New_York', DateHelper::timezoneForFormat($format, 'America/New_York'));
    }

    public static function dstValuesProvider(): array
    {
        return [
            // 2026-03-29 02:00 -> 03:00 in Europe/Madrid (02:xx does not exist locally)
            'eloquent UTC during spring gap' => ['2026-03-29T02:30:00.000000Z', DateFormat::datetime_eloquent->value],
            'eloquent UTC before spring gap' => ['2026-03-29T01:30:00.000000Z', DateFormat::datetime_eloquent->value],
            'local non-existent wall time'   => ['2026-03-29 02:30:00', DateFormat::datetime_YMD->value],
            'local non-existent with micros' => ['2026-03-29 02:30:00.000000', DateFormat::datetime_micro->value],
            // 2026-10-25 03:00 -> 02:00 in Europe/Madrid (02:xx happens twice)
            'local ambiguous wall time'      => ['2026-10-25 02:30:00', DateFormat::datetime_YMD->value],
            'eloquent UTC during autumn'     => ['2026-10-25T00:30:00.000000Z', DateFormat::datetime_eloquent->value],
        ];
    }

    #[DataProvider('dstValuesProvider')]
    public function test_matches_format_does_not_fail_on_dst_transitions(string $date, string $format): void
    {
        $this->assertTrue(DateHelper::matchesFormat($date, $format));
    }

    public function test_matches_format_rejects_invalid_values(): void
    {
        $this->assertFalse(DateHelper::matchesFormat('2026-02-30 10:00:00', DateFormat::datetime_YMD->value));
        $this->assertFalse(DateHelper::matchesFormat('2026-03-29', DateFormat::datetime_YMD->value));
        $this->assertFalse(DateHelper::matchesFormat('2026-03-29 10:00:00', DateFormat::datetime_eloquent->value));
        $this->assertFalse(DateHelper::matchesFormat('not a date', DateFormat::date_YMD->value));
    }

    public function test_matches_any_format_and_first_matching_format(): void
    {
        $formats = [DateFormat::datetime_micro->value, DateFormat::datetime_eloquent->value];

        $this->assertTrue(DateHelper::matchesAnyFormat('2026-03-29T02:30:00.000000Z', $formats));
        $this->assertSame(DateFormat::datetime_eloquent->value, DateHelper::firstMatchingFormat('2026-03-29T02:30:00.000000Z', $formats));
        $this->assertSame(DateFormat::datetime_micro->value, DateHelper::firstMatchingFormat('2026-03-29 02:30:00.000000', $formats));
        $this->assertNull(DateHelper::firstMatchingFormat('29/03/2026', $formats));
        $this->assertFalse(DateHelper::matchesAnyFormat('29/03/2026', $formats));
    }

    public function test_create_from_format_uses_the_timezone_implied_by_the_format(): void
    {
        $utc = DateHelper::createFromFormat(DateFormat::datetime_eloquent->value, '2026-03-29T02:30:00.000000Z');
        $this->assertSame('UTC', $utc->getTimezone()->getName());
        $this->assertSame('2026-03-29 04:30:00', $utc->tz('Europe/Madrid')->format('Y-m-d H:i:s'));

        $local = DateHelper::createFromFormat(DateFormat::datetime_YMD->value, '2026-03-29 04:30:00');
        $this->assertSame('Europe/Madrid', $local->getTimezone()->getName());
        $this->assertTrue($utc->eq($local));
    }

    public function test_create_from_format_with_timezone(): void
    {
        // Zoneless strings are interpreted in the given timezone
        $utc = DateHelper::createFromFormat(DateFormat::datetime_YMD->value, '2026-03-29 02:30:00', 'UTC');
        $this->assertSame('UTC', $utc->getTimezone()->getName());
        $this->assertSame('2026-03-29T02:30:00+00:00', $utc->toIso8601String());

        // \Z is always UTC
        $zulu = DateHelper::createFromFormat(DateFormat::datetime_eloquent->value, '2026-03-29T02:30:00.000000Z', 'America/New_York');
        $this->assertSame('UTC', $zulu->getTimezone()->getName());
        $this->assertTrue($utc->eq($zulu));
    }

    public function test_create_from_format_respects_the_exact_format_and_resets_unparsed_fields(): void
    {
        // d/m/Y is never interpreted as m/d/Y
        $this->assertSame('2026-11-05 00:00:00', DateHelper::createFromFormat(DateFormat::date_DMY_slash->value, '05/11/2026')->format('Y-m-d H:i:s'));
        // Date-only formats => 00:00:00
        $this->assertSame('2026-11-18 00:00:00.000000', DateHelper::createFromFormat(DateFormat::date_YMD->value, '2026-11-18')->format('Y-m-d H:i:s.u'));
        // Month/year => day 1
        $this->assertSame('2026-02-01', DateHelper::createFromFormat(DateFormat::date_MY_slash->value, '02/2026')->format('Y-m-d'));
        // Time-only => current date
        $this->assertSame(CarbonImmutable::now()->format('Y-m-d') . ' 10:15:00', DateHelper::createFromFormat(DateFormat::time->value, '10:15:00')->format('Y-m-d H:i:s'));
    }

    public function test_create_from_format_throws_if_the_value_does_not_match(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DateHelper::createFromFormat(DateFormat::date_YMD->value, '18/11/2026');
    }

    public function test_compare_works_with_instants_regardless_of_timezone(): void
    {
        $utc   = '2026-03-29T02:30:00.000000Z';
        $local = '2026-03-29 04:30:00';

        $this->assertTrue(DateHelper::compare($utc, '==', $local));
        $this->assertTrue(DateHelper::compare(CarbonImmutable::parse($utc), '==', Carbon::parse($local)));
        $this->assertTrue(DateHelper::compare(new \DateTimeImmutable($utc), '<', '2026-03-29 04:31:00'));
        $this->assertFalse(DateHelper::compare($utc, '!=', $local));
        $this->assertNull(DateHelper::compare(null, '==', $local));
    }

    public function test_compare_throws_with_invalid_operator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DateHelper::compare('2026-01-01', '<>', '2026-01-02');
    }

    public function test_merge_date_and_time_uses_the_application_timezone(): void
    {
        // 23:30 UTC on the 29th is already the 30th in Madrid (01:30 CEST)
        $date = CarbonImmutable::parse('2026-03-29T23:30:00Z');

        $merged = DateHelper::mergeDateAndTime($date, '10:00:00');

        $this->assertSame('Europe/Madrid', $merged->getTimezone()->getName());
        $this->assertSame('2026-03-30 10:00:00', $merged->format('Y-m-d H:i:s'));
    }

    public function test_merge_date_and_time_does_not_mutate_the_inputs(): void
    {
        $date = Carbon::parse('2026-11-18 08:00:00', 'UTC');
        $time = Carbon::parse('2026-01-01 12:34:56');

        $merged = DateHelper::mergeDateAndTime($date, $time);

        $this->assertSame('2026-11-18 12:34:56', $merged->format('Y-m-d H:i:s'));
        $this->assertSame('UTC', $date->getTimezone()->getName());
        $this->assertSame('2026-11-18 08:00:00', $date->format('Y-m-d H:i:s'));
    }
}

