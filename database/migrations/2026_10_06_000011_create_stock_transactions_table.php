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
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 50)->index(); // PURCHASE, SALE, ADJUSTMENT_ADD, ADJUSTMENT_SUB, CUSTOMER_RETURN, SUPPLIER_RETURN
            $table->integer('quantity'); // Signed integer (+/-)
            $table->integer('balance_before');
            $table->integer('balance_after');
            $table->string('reference_type', 100)->nullable()->index();
            $table->unsignedBigInteger('reference_id')->nullable()->index();
            $table->text('reason')->nullable();
            $table->timestamps();

            // Compound index for fast stock ledger audits
            $table->index(['product_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
