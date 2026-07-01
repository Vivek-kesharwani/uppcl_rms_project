<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->id();

            $table->string('source_type', 50);
            // AGENCY, BANK, PAYMENT_GATEWAY, BILLING

            $table->string('source_name', 100)->unique();
            // Agency_1, Agency_2, Agency_3, Bank_1, PaymentGateway_1, Billing_UPPCL

            $table->string('display_name', 150)->nullable();

            $table->string('daily_folder_path')->nullable();
            $table->string('monthly_folder_path')->nullable();

            $table->string('daily_file_pattern')->nullable();
            $table->string('monthly_file_pattern')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['source_type', 'is_active']);
        });

        DB::table('sources')->insert([
            [
                'source_type' => 'AGENCY',
                'source_name' => 'Agency_1',
                'display_name' => 'Agency 1',
                'daily_folder_path' => 'storage/rms/inbound/agency/agency_1/daily',
                'monthly_folder_path' => 'storage/rms/inbound/agency/agency_1/monthly',
                'daily_file_pattern' => 'Agency_1_daily_DDMMYYYY.csv',
                'monthly_file_pattern' => 'Agency_1_monthly_MMYYYY.csv',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'source_type' => 'AGENCY',
                'source_name' => 'Agency_2',
                'display_name' => 'Agency 2',
                'daily_folder_path' => 'storage/rms/inbound/agency/agency_2/daily',
                'monthly_folder_path' => 'storage/rms/inbound/agency/agency_2/monthly',
                'daily_file_pattern' => 'Agency_2_daily_DDMMYYYY.csv',
                'monthly_file_pattern' => 'Agency_2_monthly_MMYYYY.csv',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'source_type' => 'AGENCY',
                'source_name' => 'Agency_3',
                'display_name' => 'Agency 3',
                'daily_folder_path' => 'storage/rms/inbound/agency/agency_3/daily',
                'monthly_folder_path' => 'storage/rms/inbound/agency/agency_3/monthly',
                'daily_file_pattern' => 'Agency_3_daily_DDMMYYYY.csv',
                'monthly_file_pattern' => 'Agency_3_monthly_MMYYYY.csv',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'source_type' => 'BANK',
                'source_name' => 'Bank_1',
                'display_name' => 'Bank 1',
                'daily_folder_path' => 'storage/rms/inbound/bank/bank_1/daily',
                'monthly_folder_path' => 'storage/rms/inbound/bank/bank_1/monthly',
                'daily_file_pattern' => 'Bank_1_daily_DDMMYYYY.csv',
                'monthly_file_pattern' => 'Bank_1_monthly_MMYYYY.csv',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'source_type' => 'PAYMENT_GATEWAY',
                'source_name' => 'PaymentGateway_1',
                'display_name' => 'Payment Gateway 1',
                'daily_folder_path' => 'storage/rms/inbound/payment_gateway/pg_1/daily',
                'monthly_folder_path' => 'storage/rms/inbound/payment_gateway/pg_1/monthly',
                'daily_file_pattern' => 'PaymentGateway_1_daily_DDMMYYYY.csv',
                'monthly_file_pattern' => 'PaymentGateway_1_monthly_MMYYYY.csv',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'source_type' => 'BILLING',
                'source_name' => 'Billing_UPPCL',
                'display_name' => 'UPPCL Billing',
                'daily_folder_path' => 'storage/rms/inbound/billing/daily',
                'monthly_folder_path' => 'storage/rms/inbound/billing/monthly',
                'daily_file_pattern' => 'Billing_daily_DDMMYYYY.csv',
                'monthly_file_pattern' => 'Billing_monthly_MMYYYY.csv',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};