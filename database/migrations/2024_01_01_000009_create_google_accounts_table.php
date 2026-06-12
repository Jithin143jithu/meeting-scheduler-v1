<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('google_id')->unique();
            $table->string('email');
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('calendar_id')->nullable();
            $table->boolean('is_synced')->default(false);
            $table->timestamp('last_synced_at')->nullable();
            $table->boolean('sync_enabled')->default(true);
            $table->timestamps();

            $table->index('user_id');
            $table->index('google_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_accounts');
    }
};