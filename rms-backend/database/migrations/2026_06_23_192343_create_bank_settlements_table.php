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
            $table->string('utr_number', 64)->unique();
            $table->decimal('deposit_amt', 12, 2);
            $table->dateTime('credit_timestamp');
            $table->string('sender_info', 150)->nullable();
            $table->string('status', 30)->default('SETTLED');
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
