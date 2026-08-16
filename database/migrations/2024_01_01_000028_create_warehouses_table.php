<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->nullable();

            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
