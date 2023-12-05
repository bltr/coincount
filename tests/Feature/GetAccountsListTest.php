<?php

namespace Tests\Feature;

use App\Models\AccountType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAccountsListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_get_accounts_list()
    {
        $records = [
            ['name' => 'Счет Сбер', 'desc' => 'Зарплатный счет', 'type' => AccountType::ACTIVE->value],
            ['name' => 'Зарплата', 'desc' => 'Оклад', 'type' => AccountType::INCOME->value],
            ['name' => 'Продукты', 'desc' => '', 'type' => AccountType::EXPENSE->value],
        ];
        \DB::table('accounts')->insert($records);

        $response = $this->getJson('/accounts');

        $response->assertStatus(200);
        $response->assertJson($records);
    }
}
