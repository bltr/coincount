<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransactionRequest;
use App\Models\Transaction;
use Illuminate\Validation\ValidationException;

class TransactionsController extends Controller
{
    public function store(CreateTransactionRequest $request)
    {
        $desc = $request->desc;
        $entries = $request->entries;

        $has_debit = $has_credit = false;
        foreach ($entries as $entry) {
            if ($entry['type'] === 'debit') {
                $has_debit = true;
            }
            if ($entry['type'] === 'credit') {
                $has_credit = true;
            }
        }
        if (!$has_debit) {
            throw ValidationException::withMessages(['There must be at least one debit entry']);
        }
        if (!$has_credit) {
            throw ValidationException::withMessages(['There must be at least one credit entry']);
        }

        $transaction = Transaction::create(['desc' => $desc]);
        $transaction->entries()
            ->createMany($entries);

        return ['transaction_id' => $transaction->id];
    }
}
