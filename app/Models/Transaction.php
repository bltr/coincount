<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Transaction extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function newUniqueId(): string
    {
        return (string) Uuid::uuid7();
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }
}
