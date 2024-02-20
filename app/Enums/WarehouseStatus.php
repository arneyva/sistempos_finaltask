<?php

namespace App\Enums;

enum WarehouseStatus: int
{
    case Active = 1;
    case NonActive = 0;
    public static function getLabel(self $value): string
    {
        return match ($value) {
            WarehouseStatus::Active => 'Active',
            WarehouseStatus::NonActive => 'NonActive',
        };
    }
}
