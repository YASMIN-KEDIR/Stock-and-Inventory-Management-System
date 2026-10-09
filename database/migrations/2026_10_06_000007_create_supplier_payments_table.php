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
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->restrictOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('payment_number', 100)->unique();
            $table->date('payment_date')->index();
            $table->decimal('amount', 18, 2);
            $table->string('payment_method', 50)->default('CASH');
            $table->string('reference_number', 150)->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Compound index for vendor ledger reconciliation
            $table->index(['supplier_id', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
