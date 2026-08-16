<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Custom app-level notifications table (distinct from Laravel's built-in
 * notifiable trait's default schema) so we can track channel + read state
 * exactly per the SRS. Kept as a plain table + model rather than adopting
 * Laravel's Notification facade, to keep the multi-channel (database/
 * email/sms/push/whatsapp) semantics explicit and queryable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 60);
            $table->string('title', 190);
            $table->string('body', 500)->nullable();
            $table->json('data')->nullable();
            $table->enum('channel', ['database', 'email', 'sms', 'push', 'whatsapp'])->default('database');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
