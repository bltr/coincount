<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('transaction_id');
            $table->enum('type', \App\Models\EntryType::values());
            $table->unsignedBigInteger('amount');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
