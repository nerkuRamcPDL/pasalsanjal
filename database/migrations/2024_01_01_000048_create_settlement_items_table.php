<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settlement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('settlement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commission_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 15, 2);

            $table->index('settlement_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlement_items');
    }
};
