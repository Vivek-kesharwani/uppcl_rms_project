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
        Schema::create('agency_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('merchant_txn_ref', 64)->index();
            $table->string('consumer_no', 20)->index();
            $table->decimal('collected_amt', 12, 2);
            $table->dateTime('txn_timestamp');
            $table->string('agency_id', 30);
            $table->string('settlement_utr', 64)->nullable()->index();
            $table->string('status', 30)->default('PENDING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_transactions');
    }
};
