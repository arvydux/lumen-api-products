<?php

namespace App\Enum;

enum VatRates: int
{
    case TEN = 10;
    case TWENTY = 20;
    case TWENTY_ONE = 21;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
