<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reconciliation_batches', function (Blueprint $table) {
            $table->enum('run_mode', [
                'MANUAL',
                'SCHEDULED',
                'RETRY'
            ])->default('MANUAL')->after('status');

            $table->foreignId('triggered_by')
                ->nullable()
                ->after('run_mode')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['run_mode']);
            $table->index(['triggered_by']);
        });
    }

    public function down(): void
    {
        Schema::table('reconciliation_batches', function (Blueprint $table) {
            $table->dropForeign(['triggered_by']);
            $table->dropIndex(['run_mode']);
            $table->dropIndex(['triggered_by']);

            $table->dropColumn([
                'run_mode',
                'triggered_by',
            ]);
        });
    }
};