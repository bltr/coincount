<?php

namespace Tests\Feature;

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
            ['id' => '018eae87-7984-7291-891d-ddd0c0334d3b', 'name' => 'Сбер', 'desc' => 'Зарплатный счет', 'type' => 'active'],
            ['id' => '018eae87-7984-7291-891d-ddd0c0334d3d', 'name' => 'Наличные', 'desc' => '', 'type' => 'active'],
            ['id' => '018eae87-7985-7310-b3d7-c6e1c53c5114', 'name' => 'Работа', 'desc' => 'Оклад', 'type' => 'income'],
        ]);

        // act
        $response = $this->postJson('/transactions', [
            'desc' => 'Зарплата',
            'entries' => [
                ['type' => 'debit', 'account_id' => '018eae87-7984-7291-891d-ddd0c0334d3b', 'amount' => 40000],
                ['type' => 'debit', 'account_id' => '018eae87-7984-7291-891d-ddd0c0334d3d', 'amount' => 10000],
                ['type' => 'credit', 'account_id' => '018eae87-7985-7310-b3d7-c6e1c53c5114', 'amount' => 50000],
            ]
        ]);

        // assert
        $response->assertOk();
        $this->assertDatabaseHas('transactions', ['desc' => 'Зарплата']);
        $this->assertDatabaseHas('entries', [
            'transaction_id' => $response->json('transaction_id'),
            'account_id' => '018eae87-7984-7291-891d-ddd0c0334d3b',
            'type' => 'debit',
            'amount' => 40000
        ]);
        $this->assertDatabaseHas('entries', [
            'transaction_id' => $response->json('transaction_id'),
            'account_id' => '018eae87-7984-7291-891d-ddd0c0334d3d',
            'type' => 'debit',
            'amount' => 10000
        ]);
        $this->assertDatabaseHas('entries', [
            'transaction_id' => $response->json('transaction_id'),
            'account_id' => '018eae87-7985-7310-b3d7-c6e1c53c5114',
            'type' => 'credit',
            'amount' => 50000
        ]);
    }

    /** @test */
    public function must_be_at_least_one_debit_entry(): void
    {
        // arrange
        \DB::table('accounts')->insert([
            ['id' => '018eae87-7985-7310-b3d7-c6e1c53c5114', 'name' => 'Работа', 'desc' => 'Оклад', 'type' => 'income'],
        ]);

        // act
        $response = $this->postJson('/transactions', [
            'desc' => 'Зарплата',
            'entries' => [
                ['type' => 'credit', 'account_id' => '018eae87-7985-7310-b3d7-c6e1c53c5114', 'amount' => 50000],
            ]
        ]);

        // assert
        $response->assertUnprocessable();
    }

    /** @test */
    public function must_be_at_least_one_credit_entry(): void
    {
        // arrange
        \DB::table('accounts')->insert([
            ['id' => '018eae87-7984-7291-891d-ddd0c0334d3b', 'name' => 'Сбер', 'desc' => 'Зарплатный счет', 'type' => 'active'],
        ]);

        // act
        $response = $this->postJson('/transactions', [
            'desc' => 'Зарплата',
            'entries' => [
                ['type' => 'debit', 'account_id' => '018eae87-7984-7291-891d-ddd0c0334d3b', 'amount' => 50000],
            ]
        ]);

        // assert
        $response->assertUnprocessable();
    }

    /** @test */
    public function sum_of_credit_entries_must_be_equal_sum_of_debit_entry()
    {
        // arrange
        \DB::table('accounts')->insert([
            ['id' => '018eae87-7984-7291-891d-ddd0c0334d3b', 'name' => 'Сбер', 'desc' => 'Зарплатный счет', 'type' => 'active'],
            ['id' => '018eae87-7984-7291-891d-ddd0c0334d3d', 'name' => 'Наличные', 'desc' => '', 'type' => 'active'],
            ['id' => '018eae87-7985-7310-b3d7-c6e1c53c5114', 'name' => 'Работа', 'desc' => 'Оклад', 'type' => 'income'],
        ]);

        // act
        $response = $this->postJson('/transactions', [
            'desc' => 'Зарплата',
            'entries' => [
                ['type' => 'debit', 'account_id' => '018eae87-7984-7291-891d-ddd0c0334d3b', 'amount' => 40000],
                ['type' => 'debit', 'account_id' => '018eae87-7984-7291-891d-ddd0c0334d3d', 'amount' => 30000],
                ['type' => 'credit', 'account_id' => '018eae87-7985-7310-b3d7-c6e1c53c5114', 'amount' => 50000],
            ]
        ]);

        $response->assertUnprocessable();
    }

    /** @test */
    public function it_dont_require_transaction_description()
    {
        // arrange
        \DB::table('accounts')->insert([
            ['id' => '018eae87-7984-7291-891d-ddd0c0334d3b', 'name' => 'Сбер', 'desc' => 'Зарплатный счет', 'type' => 'active'],
            ['id' => '018eae87-7985-7310-b3d7-c6e1c53c5114', 'name' => 'Работа', 'desc' => 'Оклад', 'type' => 'income'],
        ]);

        // act
        $response = $this->postJson('/transactions', [
            //'desc' => 'Зарплата',
            'entries' => [
                ['type' => 'debit', 'account_id' => '018eae87-7984-7291-891d-ddd0c0334d3b', 'amount' => 50000],
                ['type' => 'credit', 'account_id' => '018eae87-7985-7310-b3d7-c6e1c53c5114', 'amount' => 50000],
            ]
        ]);

        // assert
        $response->assertOk();
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function it_validate_with_expected_results($field, $message, $data)
    {
        // arrange
        \DB::table('accounts')->insert([
            ['id' => '018eae87-7984-7291-891d-ddd0c0334d3b', 'name' => 'Сбер', 'desc' => 'Зарплатный счет', 'type' => 'active'],
            ['id' => '018eae87-7985-7310-b3d7-c6e1c53c5114', 'name' => 'Работа', 'desc' => 'Оклад', 'type' => 'active'],
        ]);

        // act
        $response = $this->postJson('/transactions', $data);

        // assert
        $response->assertUnprocessable();
        $response->assertInvalid($field);
        $this->assertTrue(str_contains($response->json('message'), $message));
    }

    public function dataProvider():array
    {
        return [
            'entries is array' => [
                'field' => 'entries',
                'message' => 'must be an array',
                'data' => [
                    'entries' => 'asdf',
                ]
            ],
            'amount is required' => [
                'field' => 'entries.0.amount',
                'message' => 'is required',
                'data' => [
                    'desc' => 'd',
                    'entries' => [
                        ['type' => 'debit', 'account_id' => '018eae87-7984-7291-891d-ddd0c0334d3b'],
                    ]
                ]
            ],
            'amount must be an integer' => [
                'field' => 'entries.0.amount',
                'message' => 'must be an integer',
                'data' => [
                    'desc' => 'd',
                    'entries' => [
                        ['type' => 'debit', 'account_id' => '018eae87-7984-7291-891d-ddd0c0334d3b', 'amount' => 'asdfa'],
                    ]
                ]
            ],
            'account_id is required' => [
                'error' => 'entries.0.account_id',
                'message' => 'is required',
                'data' => [
                    'desc' => 'Зарплата',
                    'entries' => [
                        ['type' => 'debit', 'amount' => 50000],
                    ],
                ]
            ],
            'account_id must be a valid uuid' => [
                'error' => 'entries.0.account_id',
                'message' => 'must be a valid UUID',
                'data' => [
                    'desc' => 'Зарплата',
                    'entries' => [
                        ['type' => 'debit', 'account_id' => 'asdfasdfasd', 'amount' => 5000],
                    ],
                ]
            ],
            'account_id must be existent id in entries table' => [
                'error' => 'entries.0.account_id',
                'message' => 'is invalid',
                'data' => [
                    'desc' => 'Зарплата',
                    'entries' => [
                        ['type' => 'debit', 'account_id' => '018eae87-7984-7291-891d-cccccccccccc', 'amount' => 5000],
                    ],
                ],
            ],
            'type must be debit or credit' => [
                'error' => 'entries.0.type',
                'message' => 'is invalid',
                'data' => [
                    'desc' => 'Зарплата',
                    'entries' => [
                        ['type' => 'asdf', 'account_id' => '018eae87-7984-7291-891d-ddd0c0334d3b', 'amount' => 5000],
                    ],
                ],
            ]
        ];
    }
}
