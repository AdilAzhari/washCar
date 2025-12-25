<?php

declare(strict_types=1);

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
        Schema::table('washes', function (Blueprint $table): void {
            $table->decimal('total_amount', 10, 2)->default(0)->after('package_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('washes', function (Blueprint $table): void {
            $table->dropColumn('total_amount');
        });
    }
};
