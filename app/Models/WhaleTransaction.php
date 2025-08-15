<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('symbol', 10);
            $table->enum('condition', ['above','below']);
            $table->decimal('threshold', 18, 8);
            $table->enum('channel', ['email','telegram'])->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();

            $table->index(['user_id','symbol']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
