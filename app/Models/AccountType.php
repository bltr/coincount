<?php

namespace App\Models;

enum AccountType: string
{
    case ACTIVE = 'active';
    case INCOME = 'income';
    case EXPENSE = 'expense';
    case COMMITMENT = 'commitment';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
