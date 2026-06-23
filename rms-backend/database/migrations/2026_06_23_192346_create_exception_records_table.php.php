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
        Schema::create('exception_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reconciliation_master_id')->constrained('reconciliation_masters')->cascadeOnDelete();
            $table->string('exception_code', 30);
            $table->decimal('variance_amount', 12, 2)->default(0.00);
            $table->string('status', 20)->default('OPEN');
            $table->string('assigned_role', 30);
            $table->unsignedBigInteger('resolved_by_user')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exception_records');
    }
};
