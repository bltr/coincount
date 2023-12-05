<?php

namespace App\Models;

enum EntryType: string
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
