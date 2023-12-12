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

    /** @test */
    public function it_validates_transactions_when_creating(): void
    {
        // act
        $response = $this->postJson('/transactions', []);

        // assert
        $response->assertStatus(422);
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function it_validate_with_expected_results($error, $message, $data)
    {
        // arrange
        \DB::table('accounts')->insert([
            ['id' => '018eae87-7984-7291-891d-ddd0c0334d3b', 'name' => 'Сбер', 'desc' => 'Зарплатный счет', 'type' => AccountType::ACTIVE->value],
            ['id' => '018eae87-7985-7310-b3d7-c6e1c53c5114', 'name' => 'Работа', 'desc' => 'Оклад', 'type' => AccountType::INCOME->value],
        ]);

        // act
        $response = $this->postJson('/transactions', $data);

        // assert
        $response->assertUnprocessable();
        $response->assertInvalid($error);
        $this->assertTrue(str_contains($response->json('message'), $message));
    }

    public function dataProvider():array
    {
        return [
            'ammount is required' => [
                'field' => 'amount',
                'message' => 'is required',
                'data' => [
                    'desc' => 'Зарплата',
                    'debit_account_id' => '018eae87-7984-7291-891d-ddd0c0334d3b',
                    'credit_account_id' => '018eae87-7985-7310-b3d7-c6e1c53c5114',
                ]
            ],
            'ammount must be an integer' => [
                'field' => 'amount',
                'message' => 'must be an integer',
                'data' => [
                    'desc' => 'Зарплата',
                    'debit_account_id' => '018eae87-7984-7291-891d-ddd0c0334d3b',
                    'credit_account_id' => '018eae87-7985-7310-b3d7-c6e1c53c5114',
                    'amount' => 'asdf',
                ]
            ],
            'credit_account_id is required' => [
                'error' => 'credit_account_id',
                'message' => 'is required',
                'data' => [
                    'desc' => 'Зарплата',
                    'debit_account_id' => '018eae87-7984-7291-891d-ddd0c0334d3b',
                    'amount' => 50
                ]
            ],
            'credit_account_id must be a valid uuid' => [
                'error' => 'credit_account_id',
                'message' => 'must be a valid UUID',
                'data' => [
                    'desc' => 'Зарплата',
                    'debit_account_id' => '018eae87-7984-7291-891d-ddd0c0334d3b',
                    'credit_account_id' => 'asdfasdf',
                    'amount' => 50
                ]
            ],
            'credit_account_id must be existent id in entries table' => [
                'error' => 'credit_account_id',
                'message' => 'is invalid',
                'data' => [
                    'desc' => 'Зарплата',
                    'debit_account_id' => '018eae87-7984-7291-891d-ddd0c0334d3b',
                    'credit_account_id' =>'018eae87-7985-7310-b3d7-cccccccccccc',
                    'amount' => 50
                ],
            ],
            'debit_account_id is required' => [
                'error' => 'debit_account_id',
                'message' => 'is required',
                'data' => [
                    'desc' => 'Зарплата',
                    'credit_account_id' => '018eae87-7985-7310-b3d7-c6e1c53c5114',
                    'amount' => 50
                ]
            ],
            'debit_account_id must be a valid uuid' => [
                'error' => 'debit_account_id',
                'message' => 'must be a valid UUID',
                'data' => [
                    'desc' => 'Зарплата',
                    'debit_account_id' => 'asdfasdf',
                    'credit_account_id' => '018eae87-7985-7310-b3d7-c6e1c53c5114',
                    'amount' => 50
                ]
            ],
            'debit_account_id must be existent id in entries table' => [
                'error' => 'debit_account_id',
                'message' => 'is invalid',
                'data' => [
                    'desc' => 'Зарплата',
                    'debit_account_id' =>  '018eae87-7984-7291-891d-cccccccccccc',
                    'credit_account_id' => '018eae87-7985-7310-b3d7-c6e1c53c5114',
                    'amount' => 50
                ],
            ],
        ];
    }
}
