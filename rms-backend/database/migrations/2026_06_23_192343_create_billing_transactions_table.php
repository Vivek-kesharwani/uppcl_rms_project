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
            $table->string('discom', 20);
            $table->string('account_no', 30)->index();
            $table->string('txn_id', 64)->unique();
            $table->date('txn_date');
            $table->time('txn_time');
            $table->decimal('amount', 12, 2);
            $table->string('agency_name', 100);
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
