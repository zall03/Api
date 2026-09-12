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
    Schema::create('cooking_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')
              ->constrained('users')
              ->cascadeOnDelete();
        $table->foreignId('recipe_id')
              ->constrained('recipes')
              ->restrictOnDelete();
        $table->decimal('servings', 4, 1)->default(1);
        $table->timestamp('cooked_at')->useCurrent();
    });
}

public function down(): void
{
    Schema::dropIfExists('cooking_logs');
}
};
