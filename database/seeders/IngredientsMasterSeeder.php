<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientsMasterSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil id kategori berdasarkan nama, supaya tidak hardcode angka
        $categories = DB::table('ingredient_categories')->pluck('id', 'name');

        DB::table('ingredients_master')->insert([
            [
                'category_id' => $categories['Sayur'],
                'name' => 'Bayam',
                'default_unit' => 'gram',
                'calories_per_100g' => 23,
                'protein_g' => 2.9,
                'fat_g' => 0.4,
                'carbs_g' => 3.6,
                'iron_mg' => 3.5,
                'zinc_mg' => 0.5,
                'vitamin_a_mcg' => 469,
                'vitamin_c_mg' => 28,
                'created_at' => now(),
            ],
            [
                'category_id' => $categories['Protein'],
                'name' => 'Telur Ayam',
                'default_unit' => 'butir',
                'calories_per_100g' => 155,
                'protein_g' => 13,
                'fat_g' => 11,
                'carbs_g' => 1.1,
                'iron_mg' => 1.8,
                'zinc_mg' => 1.3,
                'vitamin_a_mcg' => 160,
                'vitamin_c_mg' => 0,
                'created_at' => now(),
            ],
            [
                'category_id' => $categories['Karbohidrat'],
                'name' => 'Nasi Putih',
                'default_unit' => 'gram',
                'calories_per_100g' => 130,
                'protein_g' => 2.7,
                'fat_g' => 0.3,
                'carbs_g' => 28,
                'iron_mg' => 0.2,
                'zinc_mg' => 0.5,
                'vitamin_a_mcg' => 0,
                'vitamin_c_mg' => 0,
                'created_at' => now(),
            ],
        ]);
    }
}