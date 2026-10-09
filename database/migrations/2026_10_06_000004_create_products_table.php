<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('name')->index();
            $table->string('sku', 100)->unique();
            $table->string('barcode', 100)->nullable()->unique();
            $table->string('unit', 50)->default('Pcs');
            $table->decimal('cost_price', 18, 2)->default(0.00);
            $table->decimal('selling_price', 18, 2)->default(0.00)->index();
            $table->integer('current_stock')->default(0)->index();
            $table->integer('minimum_stock_level')->default(5)->index();
            $table->string('image_path', 500)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            // Compound index for instant low stock detection and fast category filters
            $table->index(['current_stock', 'minimum_stock_level']);
            $table->index(['category_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
