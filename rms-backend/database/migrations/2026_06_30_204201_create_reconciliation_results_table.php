<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reconciliation_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('batch_id')
                ->constrained('reconciliation_batches')
                ->cascadeOnDelete();

            $table->foreignId('matching_set_id')
                ->constrained('matching_sets')
                ->cascadeOnDelete();

            $table->foreignId('left_source_id')
                ->constrained('sources')
                ->cascadeOnDelete();

            $table->foreignId('right_source_id')
                ->constrained('sources')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('left_record_id')->nullable();
            $table->unsignedBigInteger('right_record_id')->nullable();

            $table->string('transaction_id', 100)->nullable();
            $table->string('consumer_number', 100)->nullable();
            $table->string('settlement_ref', 150)->nullable();
            $table->string('utr_number', 150)->nullable();

            $table->enum('period_type', ['DAILY', 'MONTHLY']);
            $table->date('business_date')->nullable();
            $table->string('business_month', 6)->nullable();

            $table->enum('result_status', [
                'MATCHED',
                'UNMATCHED',
                'PARTIAL_MATCH',
                'EXCEPTION'
            ]);

            $table->string('exception_code', 100)->nullable();
            $table->decimal('variance_amount', 15, 2)->default(0.00);

            $table->foreignId('visible_to_source_id')
                ->nullable()
                ->constrained('sources')
                ->nullOnDelete();

            $table->json('rule_results')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(['batch_id', 'matching_set_id']);
            $table->index(['left_source_id', 'right_source_id']);
            $table->index(['result_status']);
            $table->index(['exception_code']);
            $table->index(['business_date']);
            $table->index(['business_month']);
            $table->index(['transaction_id']);
            $table->index(['consumer_number']);
            $table->index(['settlement_ref']);
            $table->index(['visible_to_source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reconciliation_results');
    }
};