<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('plate_number');
            $table->string('vehicle_type')->default('sedan');
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('color')->nullable();
            $table->string('membership')->default('Regular');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index(['phone', 'plate_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
