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
        Schema::create('appointment_reminders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['sms', 'email'])->default('email');
            $table->enum('reminder_type', ['24h_before', '1h_before', 'ready'])->default('24h_before');
            $table->timestamp('sent_at')->nullable();
            $table->boolean('delivered')->default(false);
            $table->text('error')->nullable();
            $table->timestamps();

            // Index for querying pending reminders
            $table->index(['appointment_id', 'reminder_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_reminders');
    }
};
