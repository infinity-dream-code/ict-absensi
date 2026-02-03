<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Jenis = dihitung di dashboard (1) / tidak dihitung (0). Default 1 (centang).
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'jenis')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('jenis')->default(true)->after('role');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'jenis')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('jenis');
            });
        }
    }
};
