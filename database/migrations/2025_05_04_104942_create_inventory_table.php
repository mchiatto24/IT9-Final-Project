<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();               // item_ID
            $table->string('name');
            $table->string('category');
            $table->integer('quantity')->default(0);
            $table->integer('reorder_level')->default(10); // Updated to reflect the new column name
            $table->integer('reorder_quantity')->default(50);
            $table->integer('low_stock_threshold')->default(10);
            // link to suppliers
            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
