<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('child_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->decimal('weight', 5, 2);
            $table->decimal('height', 5, 1);
            $table->date('measured_at');
            $table->timestamps();

            $table->index(['child_id', 'measured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('child_measurements');
    }
};