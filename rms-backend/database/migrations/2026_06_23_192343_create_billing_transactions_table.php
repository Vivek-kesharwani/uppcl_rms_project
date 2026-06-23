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
        Schema::create('billing_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('merchant_txn_ref', 64)->index();
            $table->string('consumer_no', 20)->index();
            $table->decimal('posted_amt', 12, 2);
            $table->dateTime('posting_timestamp');
            $table->string('discom_code', 20);
            $table->string('status', 30)->default('POSTED');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_transactions');
    }
};
