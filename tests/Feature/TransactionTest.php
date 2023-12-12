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
        // arrange
        \DB::table('accounts')->insert([
            ['id' => '018eae87-7984-7291-891d-ddd0c0334d3b', 'name' => 'Сбер', 'desc' => 'Зарплатный счет', 'type' => AccountType::ACTIVE->value],
            ['id' => '018eae87-7985-7310-b3d7-c6e1c53c5114', 'name' => 'Работа', 'desc' => 'Оклад', 'type' => AccountType::INCOME->value],
        ]);

        // act
        $response = $this->postJson('/transactions', [
            'desc' => 'Зарплата',
            'debit_account_id' => '018eae87-7984-7291-891d-ddd0c0334d3b',
            'credit_account_id' => '018eae87-7985-7310-b3d7-c6e1c53c5114',
            'amount' => 50.000,
        ]);

        // assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('transactions', ['desc' => 'Зарплата']);
        $this->assertDatabaseHas('entries', [
            'account_id' => '018eae87-7984-7291-891d-ddd0c0334d3b',
            'type' => EntryType::DEBIT->value,
            'amount' => 50.000
        ]);
        $this->assertDatabaseHas('entries', [
            'account_id' => '018eae87-7985-7310-b3d7-c6e1c53c5114',
            'type' => EntryType::CREDIT->value,
            'amount' => 50.000
        ]);
    }
}
