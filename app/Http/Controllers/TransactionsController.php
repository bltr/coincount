<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\EntryType;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function store(Request $request)
    {
        $transaction = Transaction::create(['desc' => $request->desc]);
        $transaction->entries()
            ->createMany([
                ['account_id' => $request->credit_account_id, 'type' => EntryType::CREDIT, 'amount' => $request->amount],
                ['account_id' => $request->debit_account_id, 'type' => EntryType::DEBIT, 'amount' => $request->amount],
            ]);
    }
}
