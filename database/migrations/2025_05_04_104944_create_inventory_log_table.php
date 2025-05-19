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
        Schema::create('inventory_log', function (Blueprint $table) {
            $table->id('log_id');        // log_ID
            $table->enum('action', ['add', 'remove', 'adjust']); // Action type
            $table->integer('change_qty');  // Quantity change
            $table->timestamp('log_date')->useCurrent();  // Custom log date column
            $table->foreignId('item_id')
                ->constrained('inventory')  // Foreign key to inventory table
                ->onDelete('cascade');  // Cascade delete on inventory removal
            $table->foreignId('staff_id')
                ->constrained('users')  // Foreign key to users table (staff member)
                ->onDelete('cascade');  // Cascade delete on staff user removal
            $table->timestamps();  // Default created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_log');
    }
};