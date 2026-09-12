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
    Schema::create('recipes', function (Blueprint $table) {
        $table->id();
        $table->string('name', 150);
        $table->text('description')->nullable();
        $table->text('instructions');
        $table->unsignedInteger('cook_time_minutes')->nullable();
        $table->enum('source', ['manual', 'ai_generated'])->default('manual');
        $table->foreignId('created_by')
              ->nullable()
              ->constrained('users')
              ->nullOnDelete();
        $table->timestamp('created_at')->useCurrent();
    });
}

public function down(): void
{
    Schema::dropIfExists('recipes');
}
};
