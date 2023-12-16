<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransactionRequest;
use App\Models\Transaction;

class TransactionsController extends Controller
{
    public function store(CreateTransactionRequest $request)
    {
        ['desc' =>$desc, 'entries' => $entries] = $request->all();

        $transaction = Transaction::create(['desc' => $desc]);
        $transaction->entries()
            ->createMany($entries);

        return ['transaction_id' => $transaction->id];
    }
}
