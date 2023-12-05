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
        Entry::create(['transaction_id' => $transaction->id, 'account_id' => $request->credit_entry_id, 'type' => EntryType::CREDIT, 'amount' => $request->amount]);
        Entry::create(['transaction_id' => $transaction->id, 'account_id' => $request->debit_entry_id, 'type' => EntryType::DEBIT, 'amount' => $request->amount]);
    }
}
