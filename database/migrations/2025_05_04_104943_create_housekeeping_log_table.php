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
        Schema::create('housekeeping_log', function (Blueprint $table) {
            $table->id('hk_log_id');    // surrogate PK
            $table->timestamp('log_date')->useCurrent();
            $table->enum('status', ['dirty','in-progress','clean'])->default('dirty');
            $table->foreignId('staff_id')  // refers to users table
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('room_id')
                ->constrained('rooms')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('housekeeping_log');
    }
};
