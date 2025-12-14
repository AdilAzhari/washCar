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
        Schema::table('branches', function (Blueprint $table) {
            $table->string('manager_name')->nullable()->after('operating_hours');
            $table->string('manager_contact')->nullable()->after('manager_name');
            $table->time('opening_time')->nullable()->after('manager_contact');
            $table->time('closing_time')->nullable()->after('opening_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['manager_name', 'manager_contact', 'opening_time', 'closing_time']);
        });
    }
};
