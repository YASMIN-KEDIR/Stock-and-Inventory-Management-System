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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('purchase_number', 100)->unique();
            $table->date('purchase_date')->index();
            $table->decimal('subtotal', 18, 2)->default(0.00);
            $table->decimal('tax_amount', 18, 2)->default(0.00);
            $table->decimal('shipping_cost', 18, 2)->default(0.00);
            $table->decimal('discount_amount', 18, 2)->default(0.00);
            $table->decimal('grand_total', 18, 2)->default(0.00)->index();
            $table->decimal('amount_paid', 18, 2)->default(0.00);
            $table->decimal('remaining_balance', 18, 2)->default(0.00)->index();
            $table->string('payment_status', 30)->default('UNPAID')->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Compound index for date-range procurement reports and payment status
            $table->index(['purchase_date', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
