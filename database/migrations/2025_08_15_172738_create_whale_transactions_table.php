<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whale_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tx_hash')->nullable()->index();
            $table->string('wallet')->index();
            $table->string('token', 16)->index();
            $table->decimal('amount', 24, 8);
            $table->enum('direction', ['inflow','outflow'])->index();
            $table->timestamp('occurred_at')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whale_transactions');
    }
};
