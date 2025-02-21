<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bill_detail_toppings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('topping_id');
            $table->uuid('bill_detail_id');
            $table->integer('qty');
            $table->bigInteger('price');
            $table->timestamps();

            $table->foreign('topping_id')->references('id')->on('toppings')->onDelete('restrict');
            $table->foreign('bill_detail_id')->references('id')->on('bill_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_detail_toppings');
    }
};
