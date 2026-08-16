<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->integer('physical_stock')->default(0);
            $table->integer('reserved_stock')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->timestamp('updated_at')->nullable();

            $table->unique(['product_variant_id', 'warehouse_id']);
        });

        // MySQL CHECK constraint — Laravel's schema builder has no fluent
        // helper for this, so raw DDL for just the constraint, applied
        // after the table exists.
        DB::statement('ALTER TABLE inventories ADD CONSTRAINT chk_inventories_non_negative CHECK (physical_stock >= 0 AND reserved_stock >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
