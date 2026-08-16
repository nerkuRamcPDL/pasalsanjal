<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 190)->unique();
            $table->string('phone', 30)->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->enum('user_type', ['customer', 'vendor', 'staff', 'admin'])->default('customer');
            $table->enum('status', ['active', 'pending', 'suspended', 'blocked'])->default('pending');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('totp_secret', 64)->nullable();
            $table->boolean('totp_enabled')->default(false);
            $table->text('totp_recovery_codes')->nullable();
            $table->unsignedSmallInteger('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('user_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
