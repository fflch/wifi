<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

final class MacAddress
{
    private const HEX_PATTERN = '/^[0-9A-Fa-f]{12}$/';

    private const PRETTY_PATTERN = '/^([0-9A-Fa-f]{2}[:-]){5}[0-9A-Fa-f]{2}$/';

    public static function isValid(string $mac): bool
    {
        $clean = self::stripSeparators($mac);

        return preg_match(self::HEX_PATTERN, $clean) === 1
            || preg_match(self::PRETTY_PATTERN, $mac) === 1;
    }

    public static function normalize(string $mac): string
    {
        $clean = self::stripSeparators($mac);

        if (preg_match(self::HEX_PATTERN, $clean) !== 1) {
            return trim($mac);
        }

        $lower = Str::lower($clean);

        return implode(':', str_split($lower, 2));
    }

    public static function stripSeparators(string $mac): string
    {
        return preg_replace('/[^0-9A-Fa-f]/', '', $mac) ?? '';
    }
}
