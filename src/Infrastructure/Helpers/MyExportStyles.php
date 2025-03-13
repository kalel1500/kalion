<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Helpers;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class MyExportStyles
{
    /* --------------------------------------------------- HELPERS -------------------------------------------------- */

    /**
     * @param array $cols
     * [
     *  ['key' => 'col_name_1'],
     *  ['key' => 'col_name_2']
     * ]
     * @return array
     * [
     *  'col_name_1 => 'A',
     *  'col_name_2 => 'B',
     *  'col_name_3 => 'C',
     * ]
     */
    public static function getDictionaryColsLetters(array $cols): array
    {
        $cols = collect($cols);
        $letters = range('A', 'Z');
        return $cols->mapWithKeys(function($item, $key) use ($letters) { return [$item['key'] => $letters[$key]]; })->toArray();
    }

    public static function getRowCord(array $colsDictionary, Row $row): string
    {
        $firstCol = array_key_first($colsDictionary);
        $lastCol = array_key_last($colsDictionary);
        $firstLetter = $colsDictionary[$firstCol];
        $lastLetter = $colsDictionary[$lastCol];

        $getRowIndex = (string)$row->getRowIndex();
        return "$firstLetter$getRowIndex:$lastLetter$getRowIndex";
    }

    private static function getColumnsFromReceivedArray(Worksheet $sheet, array $colsLetters, bool $onlyNotReceived = false): array
    {
        $allKeys = array_keys($sheet->getColumnDimensions());

        // Devuelve todas si no recibe columnas
        if (empty($colsLetters)) return $allKeys;

        // Devuelve las contrarias si es true
        if ($onlyNotReceived) return array_diff($allKeys, $colsLetters);

        // Devuelve las recibidas
        return $colsLetters;
    }

    private static function applyCol(Worksheet $sheet, ?string $currentCellLetter = null, array $colsLetters = [], bool $onlyNotReceived = false): bool
    {
        $colsLetters = static::getColumnsFromReceivedArray($sheet, $colsLetters, $onlyNotReceived);
        return (is_null($currentCellLetter) || in_array($currentCellLetter, $colsLetters));
    }

    /* --------------------------------------------------- received -------------------------------------------------- */

    public static function setAlignHorizontal(Worksheet &$sheet, string $cord, string $alignment, string $currentCellLetter = null, array $colsLetters = [], bool $onlyNotReceived = false)
    {
        $check = static::applyCol($sheet, $currentCellLetter, $colsLetters, $onlyNotReceived);
        if($check) {
            $sheet->getStyle($cord)->getAlignment()->setHorizontal($alignment);
        }
    }

    public static function setAlignVertical(Worksheet &$sheet, string $cord, string $alignment, string $currentCellLetter = null, array $colsLetters = [], bool $onlyNotReceived = false)
    {
        $check = static::applyCol($sheet, $currentCellLetter, $colsLetters, $onlyNotReceived);
        if($check) {
            $sheet->getStyle($cord)->getAlignment()->setVertical($alignment);
        }
    }

    public static function setAlignIdent(Worksheet &$sheet, string $cord, int $ident, string $currentCellLetter = null, array $colsLetters = [], bool $onlyNotReceived = false)
    {
        $check = static::applyCol($sheet, $currentCellLetter, $colsLetters, $onlyNotReceived);
        if($check) {
            $sheet->getStyle($cord)->getAlignment()->setIndent($ident);
        }
    }

    public static function setBold(Worksheet &$sheet, string $cord, string $currentCellLetter = null, array $colsLetters = [], bool $onlyNotReceived = false)
    {
        $check = static::applyCol($sheet, $currentCellLetter, $colsLetters, $onlyNotReceived);
        if($check) {
            $sheet->getStyle($cord)->getFont()->setBold(true);
        }
    }

    public static function setBgColor(Worksheet &$sheet, string $cord, string $color, string $currentCellLetter = null, array $colsLetters = [], bool $onlyNotReceived = false)
    {
        $check = static::applyCol($sheet, $currentCellLetter, $colsLetters, $onlyNotReceived);
        if($check) {
            $sheet->getStyle($cord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color); // FILL_PATTERN_DARKGRAY o FILL_PATTERN_MEDIUMGRAY
        }
    }

    public static function setNumberFormat(Worksheet &$sheet, string $cord, string $format = NumberFormat::FORMAT_DATE_TIME4, string $currentCellLetter = null, array $colsLetters = [], bool $onlyNotReceived = false)
    {
        $check = static::applyCol($sheet, $currentCellLetter, $colsLetters, $onlyNotReceived);
        if($check) {
            $sheet->getStyle($cord)->getNumberFormat()->setFormatCode($format);
        }
    }

    public static function setType(Worksheet &$sheet, string $cord, string $dataType = DataType::TYPE_STRING, string $currentCellLetter = null, $colsLetters = [], bool $onlyNotReceived = false)
    {
        $check = static::applyCol($sheet, $currentCellLetter, $colsLetters, $onlyNotReceived);
        if($check) {
            $value = $sheet->getCell($cord)->getValue();
            $sheet->setCellValueExplicit(
                $cord,
                $value,
                $dataType
            );
        }
    }

    public static function setBorders(Worksheet &$sheet, string $cord, string $color = 'FFFF0000', string $currentCellLetter = null, array $colsLetters = [], bool $onlyNotReceived = false)
    {
        $check = static::applyCol($sheet, $currentCellLetter, $colsLetters, $onlyNotReceived);
        if ($check) {
            $sheet->getStyle($cord)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color($color));
        }
    }


    public static function getValue(Worksheet $sheet, string $cord)
    {
        return $sheet->getCell($cord)->getValue();
    }

    public static function setValue(Worksheet &$sheet, string $cord, $value, string $currentCellLetter = null, $colsLetters = [], bool $onlyNotReceived = false)
    {
        $check = static::applyCol($sheet, $currentCellLetter, $colsLetters, $onlyNotReceived);
        if($check) {
            $sheet->setCellValue($cord, $value);
        }
    }

}
