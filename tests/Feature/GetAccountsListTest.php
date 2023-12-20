<?php

namespace Tests\Feature;

use App\Models\AccountType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class GetAccountsListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_get_accounts_list_with_balance()
    {
        $account_records = [
            ['id' => '018eb91b-0ab0-7205-8c16-3ebae664c028', 'name' => 'Счет Сбер', 'desc' => 'Зарплатный счет', 'type' => 'active'],
            ['id' => '018eb91b-0ab2-72fc-bb09-ed9a5788e55c', 'name' => 'Зарплата', 'desc' => 'Оклад', 'type' => 'income'],
            ['id' => '018eb91b-0ab2-72fc-bb09-ed9a5805b0dc', 'name' => 'Продукты', 'desc' => '', 'type' => 'expense'],
        ];

        \DB::table('accounts')->insert($account_records);
        \DB::table('transactions')->insert(['id' => '018eb91b-0ab2-72fc-bb09-ed9a58387bcd']);
        \DB::table('entries')->insert([
            [
                'id' => '018eb91b-0ab2-72fc-bb09-ed9a5909192c',
                'transaction_id' => '018eb91b-0ab2-72fc-bb09-ed9a58387bcd',
                'account_id' => '018eb91b-0ab0-7205-8c16-3ebae664c028',
                'type' => 'debit',
                'amount' => 10000
            ],
            [
                'id' => '018eb91b-0ab2-72fc-bb09-ed9a59aeea36',
                'transaction_id' => '018eb91b-0ab2-72fc-bb09-ed9a58387bcd',
                'account_id' => '018eb91b-0ab0-7205-8c16-3ebae664c028',
                'type' => 'debit',
                'amount' => 5000
            ],
            [
                'id' => '018eb91b-0ab2-72fc-bb09-ed9a5a65e8f2',
                'transaction_id' => '018eb91b-0ab2-72fc-bb09-ed9a58387bcd',
                'account_id' => '018eb91b-0ab0-7205-8c16-3ebae664c028',
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
