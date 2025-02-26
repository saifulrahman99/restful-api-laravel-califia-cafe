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
        Schema::table('toppings', function (Blueprint $table) {
            $table->integer('stock')->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('toppings', function (Blueprint $table) {
             $table->dropColumn('stock'); // Hapus kolom jika rollback
        });
    }
};
