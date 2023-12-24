<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class GetAccountsListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function get_accounts_list_with_balance()
    {
        define('TRANSACTION_ID', '018eb91b-0ab2-72fc-bb09-ed9a58387bcd');
        define('ACCOUNT_ID1', '018eb91b-0ab0-7205-8c16-3ebae664c028');

        $account_records = [
            ['id' => ACCOUNT_ID1, 'name' => 'Счет Сбер', 'desc' => 'Зарплатный счет', 'type' => 'active'],
            ['id' => '018eb91b-0ab2-72fc-bb09-ed9a5788e55c', 'name' => 'Зарплата', 'desc' => 'Оклад', 'type' => 'income'],
            ['id' => '018eb91b-0ab2-72fc-bb09-ed9a5805b0dc', 'name' => 'Продукты', 'desc' => '', 'type' => 'expense'],
        ];

        \DB::table('accounts')->insert($account_records);
        \DB::table('transactions')->insert(['id' => TRANSACTION_ID]);
        \DB::table('entries')->insert([
            [
                'id' => Uuid::uuid7(),
                'transaction_id' => TRANSACTION_ID,
                'account_id' => ACCOUNT_ID1,
                'type' => 'debit',
                'amount' => 10000
            ],
            [
                'id' => Uuid::uuid7(),
                'transaction_id' => TRANSACTION_ID,
                'account_id' => ACCOUNT_ID1,
                'type' => 'debit',
                'amount' => 5000
            ],
            [
                'id' => Uuid::uuid7(),
                'transaction_id' => TRANSACTION_ID,
                'account_id' => ACCOUNT_ID1,
                'type' => 'credit',
                'amount' => 3000
            ],
        ]);

        $response = $this->getJson('/accounts');

        $account_records[0]['balance'] = 12000;
        $account_records[1]['balance'] = 0;
        $account_records[2]['balance'] = 0;
        $response->assertStatus(200);
        $response->assertJson($account_records);
    }
}
