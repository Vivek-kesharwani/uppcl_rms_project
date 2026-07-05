<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('batch_id')
                ->constrained('reconciliation_batches')
                ->cascadeOnDelete();

            $table->foreignId('source_file_id')
                ->constrained('source_files')
                ->cascadeOnDelete();

            $table->foreignId('source_id')
                ->constrained('sources')
                ->cascadeOnDelete();

            $table->foreignId('matching_set_id')
                ->nullable()
                ->constrained('matching_sets')
                ->nullOnDelete();

            $table->enum('file_side', [
                'LEFT',
                'RIGHT',
                'REFERENCE',
                'SUPPORTING'
            ]);

            $table->enum('file_role', [
                'PRIMARY',
                'COMPARISON',
                'SETTLEMENT',
                'REFERENCE'
            ])->default('PRIMARY');

            $table->enum('status', [
                'SELECTED',
                'LOCKED',
                'STAGED',
                'PROCESSED',
                'FAILED'
            ])->default('SELECTED');

            $table->unsignedBigInteger('total_records')->default(0);
            $table->unsignedBigInteger('staged_records')->default(0);
            $table->unsignedBigInteger('failed_records')->default(0);

            $table->timestamp('selected_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('staged_at')->nullable();
            $table->timestamp('processed_at')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->unique([
                'batch_id',
                'source_file_id',
                'file_side'
            ], 'batch_file_unique');

            $table->index(['batch_id', 'file_side']);
            $table->index(['source_file_id']);
            $table->index(['matching_set_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_files');
    }
};