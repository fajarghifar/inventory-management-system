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

            $table->foreignIdFor(\App\Models\Supplier::class)
                ->constrained();

            $table->string('date'); // old: purchase_date
            $table->string('purchase_no'); // old: purchase_no

            $table->tinyInteger('status' )  // old: purchase_status
                ->default(0)
                ->comment('0=Pending, 1=Approved');

            $table->integer('total_amount'); // old: total_amount
            $table->foreignIdFor(\App\Models\User::class, 'created_by');
            $table->foreignIdFor(\App\Models\User::class, 'updated_by')
                ->nullable();
                $table->uuid();
                $table->foreignId("user_id")->constrained()->onDelete('cascade');
            $table->timestamps();
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
