<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reconciliation_masters', function (Blueprint $table) {
            $table->id();
            $table->string('txn_id', 64)->unique();
            $table->string('account_no', 30)->nullable();
            $table->string('discom', 20)->nullable();

            $table->foreignId('agency_transaction_id')
                ->nullable()
                ->constrained('agency_transactions')
                ->nullOnDelete();

            $table->foreignId('billing_transaction_id')
                ->nullable()
                ->constrained('billing_transactions')
                ->nullOnDelete();

            $table->foreignId('bank_settlement_id')
                ->nullable()
                ->constrained('bank_settlements')
                ->nullOnDelete();

            $table->string('recon_status', 40)->default('PENDING');
            $table->string('exception_type', 50)->nullable();
            $table->decimal('variance_amount', 12, 2)->default(0.00);
            $table->dateTime('last_evaluated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconciliation_masters');
    }
};
