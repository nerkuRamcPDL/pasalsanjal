<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('permissions')->nullable();
            $table->enum('status', ['active', 'revoked'])->default('active');
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_staff');
    }
};
