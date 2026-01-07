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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('location_name')->nullable()->after('check_out_end');
            $table->decimal('latitude', 10, 8)->nullable()->after('location_name');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->integer('radius')->default(100)->after('longitude'); // radius in meters
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['location_name', 'latitude', 'longitude', 'radius']);
        });
    }
};
