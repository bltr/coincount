<?php

use App\Models\AccountType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->string('name');
            $table->string('desc');
            $table->enum('type', AccountType::values());
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
