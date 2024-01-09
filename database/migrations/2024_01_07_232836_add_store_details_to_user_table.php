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
        Schema::table('users', function (Blueprint $table) {
            $table->string("store_name")->nullable();
            $table->string("store_address")->nullable();
            $table->string("store_phone")->nullable();
            $table->string("store_email")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn("store_name");
            $table->dropColumn("store_address");
            $table->dropColumn("store_phone");
            $table->dropColumn("store_email");
        });
    }
};
