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
        Schema::create('bank_settlements', function (Blueprint $table) {
            $table->id();
            $table->string('bank_ref_no', 64)->unique();
            $table->string('txn_id', 64)->index();
            $table->date('settlement_date');
            $table->time('settlement_time');
            $table->decimal('settled_amount', 12, 2);
            $table->string('settlement_status', 30)->default('SUCCESS');
            $table->string('payment_gateway', 100)->default('BillDesk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_settlements');
    }
};
