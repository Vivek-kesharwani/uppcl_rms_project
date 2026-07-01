<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matching_sets', function (Blueprint $table) {
            $table->id();

            $table->string('set_code', 100)->unique();
            $table->string('set_name', 150);

            $table->string('left_source_type', 50);
            $table->string('right_source_type', 50);

            $table->enum('period_type', [
                'DAILY',
                'MONTHLY',
                'BOTH'
            ])->default('BOTH');

            $table->unsignedInteger('execution_order')->default(1);
            $table->boolean('can_run_parallel')->default(true);
            $table->boolean('is_active')->default(true);

            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['left_source_type', 'right_source_type']);
            $table->index(['is_active', 'execution_order']);
        });

        DB::table('matching_sets')->insert([
            [
                'set_code' => 'AGENCY_VS_BILLING',
                'set_name' => 'Agency vs Billing',
                'left_source_type' => 'AGENCY',
                'right_source_type' => 'BILLING',
                'period_type' => 'BOTH',
                'execution_order' => 1,
                'can_run_parallel' => true,
                'is_active' => true,
                'description' => 'Matches agency transaction files with UPPCL billing transaction files for the same business date/month.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'set_code' => 'BANK_VS_PAYMENT_GATEWAY',
                'set_name' => 'Bank vs Payment Gateway',
                'left_source_type' => 'BANK',
                'right_source_type' => 'PAYMENT_GATEWAY',
                'period_type' => 'BOTH',
                'execution_order' => 2,
                'can_run_parallel' => true,
                'is_active' => true,
                'description' => 'Matches bank settlement files with payment gateway files for the same business date/month.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('matching_sets');
    }
};