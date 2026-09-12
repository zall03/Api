<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ingredient_categories')->insert([
            ['name' => 'Sayur'],
            ['name' => 'Protein'],
            ['name' => 'Karbohidrat'],
            ['name' => 'Buah'],
            ['name' => 'Bumbu'],
            ['name' => 'Dairy'],
        ]);
    }
}