<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reconciliation_result_files', function (Blueprint $table) {

            $table->id();

            $table->foreignId('batch_id')
                  ->constrained('reconciliation_batches')
                  ->cascadeOnDelete();

            $table->foreignId('matching_set_id')
                  ->constrained('matching_sets')
                  ->cascadeOnDelete();

            $table->string('result_type')->default('CSV');

            $table->string('file_name');

            $table->string('file_path');

            $table->unsignedBigInteger('file_size')->nullable();

            $table->integer('total_records')->default(0);

            $table->integer('matched_records')->default(0);

            $table->integer('exception_records')->default(0);

            $table->date('business_date')->nullable();

            $table->string('business_month')->nullable();

            $table->enum('status', [
                'GENERATING',
                'READY',
                'FAILED'
            ])->default('GENERATING');

            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            $table->index('batch_id');
            $table->index('matching_set_id');
            $table->index('business_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reconciliation_result_files');
    }
};