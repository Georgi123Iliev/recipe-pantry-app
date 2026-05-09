<?php

namespace App\Services;

class UnitConverter
{
    /**
     * Conversion multipliers to base units.
     * Weight -> grams (g)
     * Volume -> milliliters (ml)
     * Pieces -> pieces (бр.)
     */
    private static array $conversions = [
        // Weight (base: g)
        'g'               => 1,
        'kg'              => 1000,

        // Volume (base: ml)
        'ml'              => 1,
        'l'               => 1000,
        'супена лъжица'   => 15,
        'чаена лъжица'    => 5,

        // Pieces (base: бр.)
        'бр.'             => 1,
    ];

    /**
     * Map display units to their base unit type.
     */
    private static array $baseUnitMap = [
        'g'               => 'g',
        'kg'              => 'g',
        'ml'              => 'ml',
        'l'               => 'ml',
        'супена лъжица'   => 'ml',
        'чаена лъжица'    => 'ml',
        'бр.'             => 'бр.',
    ];

    public static function toBaseUnit(float $quantity, string $displayUnit): float
    {
        $multiplier = self::$conversions[$displayUnit] ?? 1;

        return round($quantity * $multiplier, 2);
    }

    public static function getBaseUnit(string $displayUnit): string
    {
        return self::$baseUnitMap[$displayUnit] ?? $displayUnit;
    }

    public static function getAvailableUnits(): array
    {
        return array_keys(self::$conversions);
    }
}
