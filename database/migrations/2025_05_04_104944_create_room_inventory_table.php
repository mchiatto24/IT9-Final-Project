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
        Schema::create('room_inventory', function (Blueprint $table) {
            $table->foreignId('room_id')
                ->constrained('rooms')
                ->onDelete('cascade');
            $table->foreignId('item_id')
                ->constrained('inventory')
                ->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->primary(['room_id','item_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_inventory');
    }
};
