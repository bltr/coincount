<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|integer',
            'credit_account_id' => 'required|uuid|exists:accounts,id',
            'debit_account_id' => 'required|uuid|exists:accounts,id',
        ];
    }
}
