<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Account extends Model
{
    use HasUuids;

    protected $cast = [
        'type' => AccountType::class
    ];

    protected $with = ['entries'];

    protected $appends = ['balance'];

    public function newUniqueId(): string
    {
        return (string) Uuid::uuid7();
    }

    public function getBalanceAttribute(): int
    {
        return $this->entries->reduce(function ($sum, $entry) {
            $value = $entry->type === EntryType::DEBIT->value
                ? $entry->amount
                : - $entry->amount;

            return $sum + $value;
        }, 0);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }
}
