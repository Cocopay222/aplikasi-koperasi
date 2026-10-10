<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_aplikasis', function (Blueprint $table) {
            if (!Schema::hasColumn('master_aplikasis', 'username')) {
                $table->string('username')->nullable()->after('link');
            }
            if (!Schema::hasColumn('master_aplikasis', 'password')) {
                $table->string('password')->nullable()->after('username');
            }
        });
    }

    public function down(): void
    {
        Schema::table('master_aplikasis', function (Blueprint $table) {
            $table->dropColumn(['username', 'password']);
        });
    }
};
