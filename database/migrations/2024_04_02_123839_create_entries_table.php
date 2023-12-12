<?php

use App\Models\EntryType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->uuid('account_id');
            $table->uuid('transaction_id');
            $table->enum('type', EntryType::values());
            $table->unsignedBigInteger('amount');

            $table->foreign('account_id')
                ->references('id')
                ->on('accounts');
            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
