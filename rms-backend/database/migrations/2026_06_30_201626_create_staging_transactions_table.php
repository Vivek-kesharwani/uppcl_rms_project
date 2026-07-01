<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staging_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('batch_id')
                ->constrained('reconciliation_batches')
                ->cascadeOnDelete();

            $table->foreignId('source_id')
                ->constrained('sources')
                ->cascadeOnDelete();

            $table->foreignId('source_file_id')
                ->nullable()
                ->constrained('source_files')
                ->nullOnDelete();

            // Common transaction fields
            $table->string('transaction_id', 100)->nullable();
            $table->string('consumer_number', 100)->nullable();
            $table->string('account_number', 100)->nullable();

            $table->decimal('amount', 15, 2)->nullable();

            $table->date('transaction_date')->nullable();
            $table->time('transaction_time')->nullable();

            // Settlement / bank / PG related fields
            $table->string('settlement_ref', 150)->nullable();
            $table->string('utr_number', 150)->nullable();
            $table->date('settlement_date')->nullable();
            $table->decimal('settlement_amount', 15, 2)->nullable();

            // Status fields
            $table->string('transaction_status', 50)->nullable();
            $table->string('payment_status', 50)->nullable();

            // File period
            $table->enum('period_type', ['DAILY', 'MONTHLY']);
            $table->date('business_date')->nullable();
            $table->string('business_month', 6)->nullable();

            // Cleaning / validation
            $table->enum('cleaning_status', [
                'RAW',
                'CLEANED',
                'INVALID'
            ])->default('RAW');

            $table->text('validation_errors')->nullable();

            // Store original row data
            $table->json('raw_payload')->nullable();

            $table->timestamps();

            /*
             * Performance indexes for 10–20 lakh rows
             */
            $table->index(['batch_id', 'source_id']);
            $table->index(['batch_id', 'period_type']);
            $table->index(['business_date']);
            $table->index(['business_month']);
            $table->index(['transaction_id']);
            $table->index(['consumer_number']);
            $table->index(['settlement_ref']);
            $table->index(['utr_number']);
            $table->index(['cleaning_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staging_transactions');
    }
};