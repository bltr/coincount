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
    public function it_get_accounts_list()
    {
        $records = [
            ['id' => Uuid::uuid7(), 'name' => 'Счет Сбер', 'desc' => 'Зарплатный счет', 'type' => AccountType::ACTIVE->value],
            ['id' => Uuid::uuid7(), 'name' => 'Зарплата', 'desc' => 'Оклад', 'type' => AccountType::INCOME->value],
            ['id' => Uuid::uuid7(), 'name' => 'Продукты', 'desc' => '', 'type' => AccountType::EXPENSE->value],
        ];
        \DB::table('accounts')->insert($records);

        $response = $this->getJson('/accounts');

        $response->assertStatus(200);
        $response->assertJson($records);
    }
}
