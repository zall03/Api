<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Sayur',
            'Protein',
            'Karbohidrat',
            'Buah',
            'Bumbu',
            'Dairy',
        ];

        foreach ($categories as $name) {
            DB::table('ingredient_categories')->updateOrInsert(
                ['name' => $name],
                ['name' => $name],
            );
        }
    }
}
