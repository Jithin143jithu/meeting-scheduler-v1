<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('note');
            $table->enum('note_type', ['internal', 'guest_visible'])->default('internal');
            $table->timestamps();

            $table->index('booking_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_notes');
    }
};