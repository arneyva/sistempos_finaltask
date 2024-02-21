<?php

namespace App\Enums;

enum UnitStatus: int
{
    case Active = 1;
    case NonActive = 0;

    public static function getLabel(self $value): string
    {
        return match ($value) {
            UnitStatus::Active => 'Active',
            UnitStatus::NonActive => 'NonActive',
        };
    }
}
