<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('domains_and_roles_tables');

        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        DB::table('domains')->insert([
            ['name' => 'HQ', 'description' => 'Headquarters domain', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'DISCOM', 'description' => 'DISCOM monitoring domain', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'AGENCY', 'description' => 'Agency ingestion domain', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('roles')->insert([
            ['name' => 'ADMIN', 'description' => 'Administrative user', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'USER', 'description' => 'Standard user', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('domains');

        Schema::create('domains_and_roles_tables', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
