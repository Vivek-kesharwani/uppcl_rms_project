<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('source_files', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Upload Status
            |--------------------------------------------------------------------------
            */

            $table->enum('file_status', [
                'UPLOADED',
                'INVALID',
                'AVAILABLE',
                'ARCHIVED',
                'DELETED'
            ])->default('UPLOADED')->after('business_month');

            /*
            |--------------------------------------------------------------------------
            | Processing Pipeline
            |--------------------------------------------------------------------------
            */

            $table->enum('processing_status', [
                'NOT_STARTED',
                'VALIDATING',
                'VALIDATED',
                'STAGING',
                'STAGED',
                'QUEUED',
                'RECONCILING',
                'COMPLETED',
                'FAILED'
            ])->default('NOT_STARTED')->after('file_status');

            /*
            |--------------------------------------------------------------------------
            | Reconciliation State
            |--------------------------------------------------------------------------
            */

            $table->enum('reconciliation_status', [
                'NOT_USED',
                'IN_BATCH',
                'PARTIALLY_RECONCILED',
                'RECONCILED'
            ])->default('NOT_USED')->after('processing_status');

            /*
            |--------------------------------------------------------------------------
            | Record Statistics
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('valid_records')->default(0)->after('total_records');

            $table->unsignedBigInteger('invalid_records')->default(0)->after('valid_records');

            $table->unsignedBigInteger('duplicate_records')->default(0)->after('invalid_records');

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('uploaded_by')->nullable()->after('failed_records');

            $table->string('uploaded_ip', 45)->nullable()->after('uploaded_by');

            /*
            |--------------------------------------------------------------------------
            | Additional timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamp('validated_at')->nullable()->after('received_at');

            $table->timestamp('staged_at')->nullable()->after('validated_at');

            $table->timestamp('reconciled_at')->nullable()->after('processing_completed_at');

            /*
            |--------------------------------------------------------------------------
            | Lock
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_locked')->default(false)->after('reconciled_at');

            /*
            |--------------------------------------------------------------------------
            | Performance Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('file_status');

            $table->index('processing_status');

            $table->index('reconciliation_status');

            $table->index(['source_id', 'business_date', 'file_type']);

            $table->index(['processing_status', 'reconciliation_status']);
        });
    }

    public function down(): void
    {
        Schema::table('source_files', function (Blueprint $table) {

            $table->dropIndex(['file_status']);
            $table->dropIndex(['processing_status']);
            $table->dropIndex(['reconciliation_status']);
            $table->dropIndex(['source_id', 'business_date', 'file_type']);
            $table->dropIndex(['processing_status', 'reconciliation_status']);

            $table->dropColumn([
                'file_status',
                'processing_status',
                'reconciliation_status',
                'valid_records',
                'invalid_records',
                'duplicate_records',
                'uploaded_by',
                'uploaded_ip',
                'validated_at',
                'staged_at',
                'reconciled_at',
                'is_locked'
            ]);
        });
    }
};