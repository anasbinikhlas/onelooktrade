<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            if (!Schema::hasColumn('alerts', 'user_id')) {
                $table->foreignId('user_id')->constrained()->cascadeOnDelete()->after('id');
            }
            if (!Schema::hasColumn('alerts', 'symbol')) {
                $table->string('symbol', 10)->after('user_id');
            }
            if (!Schema::hasColumn('alerts', 'condition')) {
                $table->enum('condition', ['above','below'])->after('symbol');
            }
            if (!Schema::hasColumn('alerts', 'threshold')) {
                $table->decimal('threshold', 24, 8)->after('condition');
            }
            if (!Schema::hasColumn('alerts', 'channel')) {
                $table->enum('channel', ['email','telegram'])->nullable()->after('threshold');
            }
            if (!Schema::hasColumn('alerts', 'active')) {
                $table->boolean('active')->default(true)->after('channel');
            }
            if (!Schema::hasColumn('alerts', 'last_triggered_at')) {
                $table->timestamp('last_triggered_at')->nullable()->after('active');
            }

            // helpful index
            if (!Schema::hasColumn('alerts', 'user_symbol_index')) {
                $table->index(['user_id', 'symbol'], 'user_symbol_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            if (Schema::hasColumn('alerts', 'last_triggered_at')) $table->dropColumn('last_triggered_at');
            if (Schema::hasColumn('alerts', 'active')) $table->dropColumn('active');
            if (Schema::hasColumn('alerts', 'channel')) $table->dropColumn('channel');
            if (Schema::hasColumn('alerts', 'threshold')) $table->dropColumn('threshold');
            if (Schema::hasColumn('alerts', 'condition')) $table->dropColumn('condition');
            if (Schema::hasColumn('alerts', 'symbol')) $table->dropColumn('symbol');
            if (Schema::hasColumn('alerts', 'user_id')) $table->dropConstrainedForeignId('user_id');
            if (Schema::hasIndex('user_symbol_index')) $table->dropIndex('user_symbol_index');
        });
    }
};
