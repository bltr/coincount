<?php

namespace App\Http\Controllers;

use App\Models\Account;

class AccountsController
{
    public function index()
    {
        $accounts = Account::all();

        return $accounts;
    }
}
