<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 255);
            $table->string('slug', 280)->unique();
            $table->enum('type', ['simple', 'variable'])->default('simple');
            $table->string('short_description', 500)->nullable();
            $table->longText('description')->nullable();
            $table->string('video_url')->nullable();
            $table->decimal('base_price', 15, 2)->default(0);
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('tax_class', 50)->nullable();
            $table->string('shipping_class', 50)->nullable();
            $table->enum('status', ['draft', 'pending_approval', 'published', 'rejected', 'inactive'])->default('draft');
            $table->string('rejection_reason', 255)->nullable();
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->string('seo_title', 190)->nullable();
            $table->string('seo_description', 255)->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('vendor_id');
            $table->index('category_id');
            $table->index('status');
            $table->fullText(['name', 'short_description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
