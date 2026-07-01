<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('source_files', function (Blueprint $table) {

            $table->id();

            // Source information
            $table->foreignId('source_id')
                  ->constrained('sources')
                  ->cascadeOnDelete();

            // File details
            $table->string('file_name');
            $table->string('file_path');

            // DAILY / MONTHLY
            $table->enum('file_type', [
                'DAILY',
                'MONTHLY'
            ]);

            // Business timeline
            $table->date('business_date')->nullable();
            $table->string('business_month', 6)->nullable();
            // Example: 062026

            // File metadata
            $table->unsignedBigInteger('file_size')->nullable();

            $table->string('checksum', 64)->nullable();

            // Processing status
            $table->enum('status', [
                'RECEIVED',
                'VALIDATING',
                'CLEANING',
                'STAGED',
                'RECONCILING',
                'COMPLETED',
                'FAILED'
            ])->default('RECEIVED');

            // Statistics
            $table->unsignedBigInteger('total_records')->default(0);

            $table->unsignedBigInteger('processed_records')->default(0);

            $table->unsignedBigInteger('failed_records')->default(0);

            // Timing
            $table->timestamp('received_at')->nullable();

            $table->timestamp('processing_started_at')->nullable();

            $table->timestamp('processing_completed_at')->nullable();

            // Error tracking
            $table->text('error_message')->nullable();

            $table->timestamps();

            /*
             * Performance Indexes
             */

            $table->index(['source_id']);

            $table->index(['status']);

            $table->index(['business_date']);

            $table->index(['business_month']);

            $table->index(['file_type']);

            $table->index([
                'source_id',
                'business_date'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('source_files');
    }
};