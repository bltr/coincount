<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransactionRequest;
use App\Models\Transaction;

class TransactionsController extends Controller
{
    public function store(CreateTransactionRequest $request)
    {
        $desc = $request->desc;
        $entries = $request->entries;

        $transaction = Transaction::create(['desc' => $desc]);
        $transaction->entries()
            ->createMany($entries);

        return ['transaction_id' => $transaction->id];
    }
}
