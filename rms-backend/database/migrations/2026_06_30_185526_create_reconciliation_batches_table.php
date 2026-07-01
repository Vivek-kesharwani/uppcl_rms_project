<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reconciliation_batches', function (Blueprint $table) {
            $table->id();

            $table->string('batch_code')->unique();

            $table->enum('batch_type', [
                'DAILY',
                'MONTHLY'
            ]);

            $table->date('business_date')->nullable();
            $table->string('business_month', 6)->nullable();

            $table->enum('status', [
                'CREATED',
                'WAITING_FOR_FILES',
                'FILES_READY',
                'CLEANING',
                'STAGING',
                'RECONCILING',
                'COMPLETED',
                'FAILED'
            ])->default('CREATED');

            $table->unsignedInteger('total_files')->default(0);
            $table->unsignedInteger('ready_files')->default(0);
            $table->unsignedBigInteger('total_records')->default(0);
            $table->unsignedBigInteger('matched_records')->default(0);
            $table->unsignedBigInteger('exception_records')->default(0);

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index(['batch_type', 'business_date']);
            $table->index(['batch_type', 'business_month']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reconciliation_batches');
    }
};