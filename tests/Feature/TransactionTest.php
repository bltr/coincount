<?php

namespace Tests\Feature;

use App\Models\AccountType;
use App\Models\EntryType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_transaction(): void
    {
        $id1 = Uuid::uuid7();
        $id2 = Uuid::uuid7();
        \DB::table('accounts')->insert([
            ['id' => $id1, 'name' => 'Сбер', 'desc' => 'Зарплатный счет', 'type' => AccountType::ACTIVE->value],
            ['id' => $id2, 'name' => 'Работа', 'desc' => 'Оклад', 'type' => AccountType::INCOME->value],
        ]);

        $response = $this->postJson('/transactions', [
            'desc' => 'Зарплата',
            'debit_account_id' => $id1,
            'credit_account_id' => $id2,
            'amount' => 50.000,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('transactions', ['desc' => 'Зарплата']);
        $this->assertDatabaseHas('entries', [
            'account_id' => $id1,
            'type' => EntryType::DEBIT->value,
            'amount' => 50.000
        ]);
        $this->assertDatabaseHas('entries', [
            'account_id' => $id2,
            'type' => EntryType::CREDIT->value,
            'amount' => 50.000
        ]);
    }
}
