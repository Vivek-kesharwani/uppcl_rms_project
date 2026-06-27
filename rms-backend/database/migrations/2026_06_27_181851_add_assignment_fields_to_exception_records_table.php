<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exception_records', function (Blueprint $table) {

            $table->string('assigned_to')->nullable()->after('assigned_role');

            $table->timestamp('assigned_at')->nullable()->after('assigned_to');

            $table->string('resolved_by')->nullable()->after('remarks');

            $table->timestamp('resolved_at')->nullable()->after('resolved_by');

        });
    }

    public function down(): void
    {
        Schema::table('exception_records', function (Blueprint $table) {

            $table->dropColumn([
                'assigned_to',
                'assigned_at',
                'resolved_by',
                'resolved_at'
            ]);

        });
    }
};