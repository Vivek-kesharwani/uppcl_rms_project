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
            $table->string('merchant_txn_ref', 64)->unique();
            $table->string('consumer_no', 20);
            $table->foreignId('agency_transaction_id')->nullable()->constrained('agency_transactions')->nullOnDelete();
            $table->foreignId('billing_transaction_id')->nullable()->constrained('billing_transactions')->nullOnDelete();
            $table->foreignId('bank_settlement_id')->nullable()->constrained('bank_settlements')->nullOnDelete();
            $table->string('master_status', 30)->default('PENDING_RUN');
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
