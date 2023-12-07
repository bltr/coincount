<?php

namespace Tests\Feature;

use App\Models\AccountType;
use App\Models\EntryType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_transaction(): void
    {
        \DB::table('accounts')->insert([
            ['name' => 'Работа', 'desc' => 'Оклад', 'type' => AccountType::INCOME->value],
            ['name' => 'Сбер', 'desc' => 'Зарплатный счет', 'type' => AccountType::ACTIVE->value],
        ]);

        $response = $this->postJson('/transactions', [
            'desc' => 'Зарплата',
            'debit_account_id' => 2,
            'credit_account_id' => 1,
            'amount' => 50.000,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('transactions', ['desc' => 'Зарплата']);
        $this->assertDatabaseHas('entries', [
            'transaction_id' => 1,
            'account_id' => 2,
            'type' => EntryType::DEBIT->value,
            'amount' => 50.000
        ]);
        $this->assertDatabaseHas('entries', [
            'transaction_id' => 1,
            'account_id' => 1,
            'type' => EntryType::CREDIT->value,
            'amount' => 50.000
        ]);
    }
}
