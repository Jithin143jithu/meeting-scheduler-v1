<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('meeting_type_id')->constrained('meeting_types')->onDelete('cascade');
            $table->string('guest_name');
            $table->string('guest_email')->unique();
            $table->string('guest_phone')->nullable();
            $table->text('guest_notes')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('location_url')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'rescheduled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('timezone');
            $table->string('meeting_link')->nullable();
            $table->text('meeting_notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('guest_email');
            $table->index('status');
            $table->index('start_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};