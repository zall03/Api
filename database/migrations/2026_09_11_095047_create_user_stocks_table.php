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
    Schema::create('user_stocks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')
              ->constrained('users')
              ->cascadeOnDelete();
        $table->foreignId('ingredient_id')
              ->constrained('ingredients_master')
              ->restrictOnDelete();
        $table->decimal('quantity', 8, 2);
        $table->string('unit', 20);
        $table->date('expiry_date')->nullable();
        $table->timestamps();

        $table->index(['user_id', 'expiry_date']); // untuk query "hampir kedaluwarsa"
    });
}

public function down(): void
{
    Schema::dropIfExists('user_stocks');
}
};
