<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('duration')->comment('Duration in minutes');
            $table->enum('location_type', ['google_meet', 'phone', 'custom_url', 'in_person'])->default('google_meet');
            $table->string('location_url')->nullable();
            $table->integer('buffer_before')->default(0)->comment('Buffer time before in minutes');
            $table->integer('buffer_after')->default(0)->comment('Buffer time after in minutes');
            $table->integer('daily_limit')->nullable()->comment('Max meetings per day');
            $table->integer('advance_booking_days')->default(365);
            $table->integer('min_booking_notice')->default(0)->comment('Minimum hours before booking');
            $table->boolean('is_active')->default(true);
            $table->string('color')->default('#3498db');
            $table->string('slug')->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_types');
    }
};