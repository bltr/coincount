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

    protected $appends = ['balance'];

    public function newUniqueId(): string
    {
        return (string) Uuid::uuid7();
    }

    public function getBalanceAttribute(): int
    {
        return $this->entries()->where('type', EntryType::DEBIT)->sum('amount') - $this->entries()->where('type', EntryType::CREDIT)->sum('amount');
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }
}
