<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->enum('type', ['flash_sale', 'seasonal', 'category', 'vendor', 'product', 'buy_x_get_y', 'free_shipping']);
            $table->foreignId('vendor_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->nullable();

            $table->index('vendor_id');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
