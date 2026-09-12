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
    Schema::create('ingredients_master', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')
              ->constrained('ingredient_categories')
              ->restrictOnDelete();
        $table->string('name', 100);
        $table->string('default_unit', 20);
        $table->decimal('calories_per_100g', 6, 2)->default(0);
        $table->decimal('protein_g', 6, 2)->default(0);
        $table->decimal('fat_g', 6, 2)->default(0);
        $table->decimal('carbs_g', 6, 2)->default(0);
        $table->decimal('iron_mg', 6, 2)->default(0);
        $table->decimal('zinc_mg', 6, 2)->default(0);
        $table->decimal('vitamin_a_mcg', 6, 2)->default(0);
        $table->decimal('vitamin_c_mg', 6, 2)->default(0);
        $table->timestamp('created_at')->useCurrent();
        $table->index('name');
    });
}

public function down(): void
{
    Schema::dropIfExists('ingredients_master');
}
};
