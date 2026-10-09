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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('invoice_number', 100)->unique();
            $table->date('sale_date')->index();
            $table->decimal('subtotal', 18, 2)->default(0.00);
            $table->decimal('discount_amount', 18, 2)->default(0.00);
            $table->decimal('tax_amount', 18, 2)->default(0.00);
            $table->decimal('grand_total', 18, 2)->default(0.00)->index();
            $table->decimal('amount_paid', 18, 2)->default(0.00);
            $table->decimal('remaining_balance', 18, 2)->default(0.00)->index();
            $table->string('payment_status', 30)->default('UNPAID')->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Compound index for date-range sales analytics and customer debt tracking
            $table->index(['sale_date', 'payment_status']);
            $table->index(['customer_id', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
