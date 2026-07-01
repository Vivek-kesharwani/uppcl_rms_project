<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matching_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('matching_set_id')
                ->constrained('matching_sets')
                ->cascadeOnDelete();

            $table->string('rule_code', 100);
            $table->string('rule_name', 150);

            $table->string('rule_group', 100)->nullable();

            $table->string('left_field', 100);
            $table->string('right_field', 100);

            $table->enum('comparison_operator', [
                'EQUAL',
                'AMOUNT_EQUAL',
                'DATE_EQUAL',
                'TIME_EQUAL',
                'TOLERANCE',
                'STATUS_EQUAL'
            ])->default('EQUAL');

            $table->decimal('tolerance_value', 12, 2)->nullable();

            $table->unsignedInteger('priority')->default(1);

            $table->boolean('is_mandatory')->default(true);
            $table->boolean('stop_on_failure')->default(true);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['matching_set_id', 'rule_code']);
            $table->index(['matching_set_id', 'is_active', 'priority']);
            $table->index('rule_group');
        });

        $agencyVsBillingId = DB::table('matching_sets')
            ->where('set_code', 'AGENCY_VS_BILLING')
            ->value('id');

        $bankVsPgId = DB::table('matching_sets')
            ->where('set_code', 'BANK_VS_PAYMENT_GATEWAY')
            ->value('id');

        DB::table('matching_rules')->insert([
            [
                'matching_set_id' => $agencyVsBillingId,
                'rule_code' => 'TXN_ID_MATCH',
                'rule_name' => 'Transaction ID Match',
                'rule_group' => 'IDENTITY',
                'left_field' => 'transaction_id',
                'right_field' => 'transaction_id',
                'comparison_operator' => 'EQUAL',
                'priority' => 1,
                'is_mandatory' => true,
                'stop_on_failure' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matching_set_id' => $agencyVsBillingId,
                'rule_code' => 'CONSUMER_MATCH',
                'rule_name' => 'Consumer Number Match',
                'rule_group' => 'IDENTITY',
                'left_field' => 'consumer_number',
                'right_field' => 'consumer_number',
                'comparison_operator' => 'EQUAL',
                'priority' => 2,
                'is_mandatory' => true,
                'stop_on_failure' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matching_set_id' => $agencyVsBillingId,
                'rule_code' => 'AMOUNT_MATCH',
                'rule_name' => 'Amount Match',
                'rule_group' => 'FINANCIAL',
                'left_field' => 'amount',
                'right_field' => 'amount',
                'comparison_operator' => 'AMOUNT_EQUAL',
                'priority' => 3,
                'is_mandatory' => true,
                'stop_on_failure' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matching_set_id' => $agencyVsBillingId,
                'rule_code' => 'DATE_MATCH',
                'rule_name' => 'Transaction Date Match',
                'rule_group' => 'TIMELINE',
                'left_field' => 'transaction_date',
                'right_field' => 'transaction_date',
                'comparison_operator' => 'DATE_EQUAL',
                'priority' => 4,
                'is_mandatory' => true,
                'stop_on_failure' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'matching_set_id' => $bankVsPgId,
                'rule_code' => 'SETTLEMENT_REF_MATCH',
                'rule_name' => 'Settlement Reference Match',
                'rule_group' => 'IDENTITY',
                'left_field' => 'settlement_ref',
                'right_field' => 'settlement_ref',
                'comparison_operator' => 'EQUAL',
                'priority' => 1,
                'is_mandatory' => true,
                'stop_on_failure' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matching_set_id' => $bankVsPgId,
                'rule_code' => 'UTR_MATCH',
                'rule_name' => 'UTR Match',
                'rule_group' => 'IDENTITY',
                'left_field' => 'utr_number',
                'right_field' => 'utr_number',
                'comparison_operator' => 'EQUAL',
                'priority' => 2,
                'is_mandatory' => false,
                'stop_on_failure' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matching_set_id' => $bankVsPgId,
                'rule_code' => 'SETTLEMENT_AMOUNT_MATCH',
                'rule_name' => 'Settlement Amount Match',
                'rule_group' => 'FINANCIAL',
                'left_field' => 'settlement_amount',
                'right_field' => 'amount',
                'comparison_operator' => 'AMOUNT_EQUAL',
                'priority' => 3,
                'is_mandatory' => true,
                'stop_on_failure' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'matching_set_id' => $bankVsPgId,
                'rule_code' => 'SETTLEMENT_DATE_MATCH',
                'rule_name' => 'Settlement Date Match',
                'rule_group' => 'TIMELINE',
                'left_field' => 'settlement_date',
                'right_field' => 'settlement_date',
                'comparison_operator' => 'DATE_EQUAL',
                'priority' => 4,
                'is_mandatory' => true,
                'stop_on_failure' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('matching_rules');
    }
};