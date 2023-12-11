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

    public function newUniqueId(): string
    {
        return (string) Uuid::uuid7();
    }
}
