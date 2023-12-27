<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Entry;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    /** @test */
    public function calc_balance(): void
    {
        $account = new Account();
        $account->setRelation('entries', collect([
            new Entry(['type' => 'debit', 'amount' => 10000]),
            new Entry(['type' => 'debit', 'amount' => 5000]),
            new Entry(['type' => 'credit', 'amount' => 3000]),
            new Entry(['type' => 'credit', 'amount' => 1000]),
        ]));

        $this->assertEquals(11000, $account->balance);
    }
}
