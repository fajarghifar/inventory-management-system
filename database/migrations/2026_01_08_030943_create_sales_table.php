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
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('sale_date')->index();
            $table->string('status')->index();
            // Financials
            $table->bigInteger('subtotal')->default(0);
            $table->bigInteger('total_discount')->default(0);
            $table->bigInteger('total')->default(0);
            // Payment Info
            $table->bigInteger('cash_received')->default(0);
            $table->bigInteger('change')->default(0);
            $table->string('payment_method')->default('cash'); // Enums\PaymentMethod
            $table->text('notes')->nullable();
            $table->timestamps();
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
