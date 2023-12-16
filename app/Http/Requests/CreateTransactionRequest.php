<?php

namespace App\Http\Requests;

use App\Models\EntryType;
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
            'entries' => 'array',
            'entries.*.amount' => 'required|integer',
            'entries.*.account_id' => 'required|uuid|exists:accounts,id',
            'entries.*.type' => 'in:' . implode(',', EntryType::values()),
        ];
    }
}
