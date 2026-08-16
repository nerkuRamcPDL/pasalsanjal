<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Commission is recorded as an immutable transaction at time of sale
 * (SRS FR-FIN-001) — never recalculated later from current settings.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_order_id')->constrained()->restrictOnDelete();
            $table->foreignId('vendor_id')->constrained()->restrictOnDelete();
            $table->decimal('commission_rate', 5, 2);
            $table->enum('commission_source', ['vendor', 'category', 'global']);
            $table->decimal('order_amount', 15, 2);
            $table->decimal('commission_amount', 15, 2);
            $table->decimal('vendor_payable', 15, 2);
            $table->foreignId('settlement_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index('vendor_order_id');
            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
