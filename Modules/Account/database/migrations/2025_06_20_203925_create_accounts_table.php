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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['checking', 'savings', 'investment'])->default('checking');
            $table->double('balance')->default(0);
            $table->dateTime('open_date');
            $table->dateTime('close_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'closed'])->default('active');
            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
