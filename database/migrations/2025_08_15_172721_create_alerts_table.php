<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // who created it
            $table->string('symbol'); // e.g. BTC/USDT
            $table->decimal('target_price', 20, 8); // price threshold
            $table->enum('condition', ['above', 'below']); // alert condition
            $table->boolean('is_active')->default(true); // active or paused
            $table->boolean('is_triggered')->default(false); // already triggered?
            $table->timestamp('triggered_at')->nullable(); // when it was triggered
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alerts');
    }
};
