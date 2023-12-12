<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransactionRequest;
use App\Models\EntryType;
use App\Models\Transaction;

class TransactionsController extends Controller
{
    public function store(CreateTransactionRequest $request)
    {
        $transaction = Transaction::create(['desc' => $request->desc]);
        $transaction->entries()
            ->createMany([
                ['account_id' => $request->credit_account_id, 'type' => EntryType::CREDIT, 'amount' => $request->amount],
                ['account_id' => $request->debit_account_id, 'type' => EntryType::DEBIT, 'amount' => $request->amount],
            ]);

        return ['transaction_id' => $transaction->id];
    }
}
