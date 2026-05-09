<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredient_recipe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->decimal('display_quantity', 8, 2);
            $table->string('display_unit');
            $table->decimal('normalized_quantity', 10, 2)->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredient_recipe');
    }
};
