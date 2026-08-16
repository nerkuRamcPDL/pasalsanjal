<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->enum('calculation_type', ['flat_rate', 'weight_based', 'location_based', 'order_value_based', 'free'])->default('flat_rate');
            $table->decimal('base_rate', 15, 2)->default(0);
            $table->decimal('free_shipping_threshold', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->nullable();

            $table->index('shipping_zone_id');
            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
