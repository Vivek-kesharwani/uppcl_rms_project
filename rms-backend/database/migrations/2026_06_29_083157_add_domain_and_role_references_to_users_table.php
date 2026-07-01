<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'domain_id')) {
                $table->foreignId('domain_id')->nullable()->after('role')->constrained('domains');
            }

            if (!Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->nullable()->after('domain_id')->constrained('roles');
            }
        });

        $hqDomain = DB::table('domains')->where('name', 'HQ')->value('id');
        $discomDomain = DB::table('domains')->where('name', 'DISCOM')->value('id');
        $agencyDomain = DB::table('domains')->where('name', 'AGENCY')->value('id');

        $adminRole = DB::table('roles')->where('name', 'ADMIN')->value('id');
        $userRole = DB::table('roles')->where('name', 'USER')->value('id');

        DB::table('users')->where('email', 'hqadmin@uppcl.com')->update([
            'domain_id' => $hqDomain,
            'role_id' => $adminRole,
        ]);

        DB::table('users')->where('email', 'discom@uppcl.com')->update([
            'domain_id' => $discomDomain,
            'role_id' => $adminRole,
        ]);

        DB::table('users')->where('email', 'agency@uppcl.com')->update([
            'domain_id' => $agencyDomain,
            'role_id' => $userRole,
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            }

            if (Schema::hasColumn('users', 'domain_id')) {
                $table->dropForeign(['domain_id']);
                $table->dropColumn('domain_id');
            }
        });
    }
};