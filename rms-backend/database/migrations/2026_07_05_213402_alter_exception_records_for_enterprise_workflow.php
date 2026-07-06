<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exception_records', function (Blueprint $table) {
            $table->foreignId('reconciliation_result_id')
                ->nullable()
                ->after('reconciliation_master_id')
                ->constrained('reconciliation_results')
                ->nullOnDelete();

            $table->string('case_number')->nullable()->after('reconciliation_result_id');

            $table->enum('priority', [
                'LOW',
                'MEDIUM',
                'HIGH',
                'CRITICAL'
            ])->default('MEDIUM')->after('severity');

            $table->enum('status', [
                'OPEN',
                'ASSIGNED',
                'IN_PROGRESS',
                'RESOLVED',
                'VERIFIED',
                'CLOSED'
            ])->default('OPEN')->change();

            $table->timestamp('opened_at')->nullable()->after('assigned_at');

            $table->timestamp('verified_at')->nullable()->after('resolved_at');
            $table->string('verified_by')->nullable()->after('verified_at');

            $table->timestamp('closed_at')->nullable()->after('verified_by');
            $table->string('closed_by')->nullable()->after('closed_at');

            $table->timestamp('sla_due_at')->nullable()->after('closed_by');
            $table->boolean('sla_breached')->default(false)->after('sla_due_at');

            $table->text('resolution_notes')->nullable()->after('remarks');
            $table->text('root_cause')->nullable()->after('resolution_notes');

            $table->index('reconciliation_result_id');
            $table->index('case_number');
            $table->index(['status', 'priority']);
            $table->index('sla_due_at');
        });
    }

    public function down(): void
    {
        Schema::table('exception_records', function (Blueprint $table) {
            $table->dropForeign(['reconciliation_result_id']);

            $table->dropIndex(['reconciliation_result_id']);
            $table->dropIndex(['case_number']);
            $table->dropIndex(['status', 'priority']);
            $table->dropIndex(['sla_due_at']);

            $table->dropColumn([
                'reconciliation_result_id',
                'case_number',
                'priority',
                'opened_at',
                'verified_at',
                'verified_by',
                'closed_at',
                'closed_by',
                'sla_due_at',
                'sla_breached',
                'resolution_notes',
                'root_cause',
            ]);
        });
    }
};