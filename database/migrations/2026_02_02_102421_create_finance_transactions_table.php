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
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // FTX.YYMMDD.0001
            $table->date('transaction_date');
            $table->foreignId('finance_category_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('amount');
            $table->text('description')->nullable();
            $table->string('external_reference')->nullable(); // Optional external ref
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};
