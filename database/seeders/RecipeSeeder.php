<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = DB::table('ingredients_master')->pluck('id', 'name');

        $recipes = [
            [
                'name' => 'Sup Ayam Wortel Kentang',
                'description' => 'Sup ayam hangat dengan sayur, sumber protein dan vitamin.',
                'cook_time_minutes' => 40,
                'instructions' => "Tumis bawang merah dan bawang putih hingga harum.\nMasukkan dada ayam, masak hingga berubah warna.\nTambahkan air, wortel, dan kentang. Masak hingga empuk.\nBeri garam, taburkan seledri dan daun bawang. Sajikan panas.",
                'ingredients' => [
                    ['Dada Ayam', 150, 'gram'],
                    ['Wortel', 100, 'gram'],
                    ['Kentang', 100, 'gram'],
                    ['Bawang Merah', 5, 'gram'],
                    ['Bawang Putih', 3, 'gram'],
                    ['Seledri', 5, 'gram'],
                    ['Daun Bawang', 5, 'gram'],
                    ['Garam', 0.5, 'sdt'],
                ],
            ],
            [
                'name' => 'Cah Bayam Bawang Putih',
                'description' => 'Bayam tumis cepat dengan bawang putih, kaya zat besi.',
                'cook_time_minutes' => 10,
                'instructions' => "Tumis bawang putih cincang dengan minyak hingga harum.\nMasukkan bayam, aduk hingga layu.\nBeri garam, angkat dan sajikan.",
                'ingredients' => [
                    ['Bayam', 150, 'gram'],
                    ['Bawang Putih', 2, 'gram'],
                    ['Minyak Goreng', 1, 'sdm'],
                    ['Garam', 0.5, 'sdt'],
                ],
            ],
            [
                'name' => 'Tumis Kangkung',
                'description' => 'Kangkung tumis terasi, pendamping nasi yang cepat dimasak.',
                'cook_time_minutes' => 10,
                'instructions' => "Tumis bawang merah, bawang putih, dan terasi hingga harum.\nMasukkan cabe rawit, aduk sebentar.\nMasukkan kangkung, aduk hingga layu, beri garam. Angkat.",
                'ingredients' => [
                    ['Kangkung', 200, 'gram'],
                    ['Bawang Merah', 3, 'gram'],
                    ['Bawang Putih', 2, 'gram'],
                    ['Cabe Rawit', 2, 'gram'],
                    ['Terasi', 0.5, 'sdm'],
                    ['Garam', 0.5, 'sdt'],
                ],
            ],
            [
                'name' => 'Capcay Sayur',
                'description' => 'Capcay aneka sayur berkuah, sumber serat dan vitamin C.',
                'cook_time_minutes' => 20,
                'instructions' => "Tumis bawang putih dan bawang bombay hingga harum.\nMasukkan wortel, kol, dan brokoli, aduk rata.\nTambahkan buncis dan sedikit air, masak hingga layu.\nBeri garam, angkat dan sajikan.",
                'ingredients' => [
                    ['Wortel', 80, 'gram'],
                    ['Kol Putih', 80, 'gram'],
                    ['Brokoli', 80, 'gram'],
                    ['Buncis', 60, 'gram'],
                    ['Bawang Putih', 2, 'gram'],
                    ['Bawang Bombay', 20, 'gram'],
                    ['Garam', 0.5, 'sdt'],
                ],
            ],
            [
                'name' => 'Omelet Tahu',
                'description' => 'Telur dadar dengan tahu, protein terjangkau untuk sarapan.',
                'cook_time_minutes' => 10,
                'instructions' => "Hancurkan tahu, campur dengan telur dan daun bawang.\nBeri garam, kocok rata.\nGoreng hingga matang di kedua sisi. Sajikan.",
                'ingredients' => [
                    ['Telur Ayam', 2, 'butir'],
                    ['Tahu Putih', 50, 'gram'],
                    ['Daun Bawang', 5, 'gram'],
                    ['Garam', 0.5, 'sdt'],
                ],
            ],
            [
                'name' => 'Sup Ikan Patin',
                'description' => 'Sup ikan patin segar dengan serai dan jahe.',
                'cook_time_minutes' => 30,
                'instructions' => "Tumis bawang merah dan bumbu hingga harum.\nMasukkan air, serai, jahe, dan daun jeruk, didihkan.\nMasukkan ikan patin dan tomat, masak hingga matang.\nBeri garam, taburi seledri. Sajikan.",
                'ingredients' => [
                    ['Ikan Patin', 200, 'gram'],
                    ['Wortel', 80, 'gram'],
                    ['Tomat', 50, 'gram'],
                    ['Serai', 1, 'batang'],
                    ['Jahe', 10, 'gram'],
                    ['Daun Jeruk', 2, 'lembar'],
                    ['Bawang Merah', 5, 'gram'],
                    ['Garam', 0.5, 'sdt'],
                ],
            ],
            [
                'name' => 'Pepes Tahu Tempe',
                'description' => 'Tahu tempe pepes bumbu kuning, masakan kukus yang menyehatkan.',
                'cook_time_minutes' => 45,
                'instructions' => "Haluskan bumbu: bawang merah, bawang putih, kemiri, jahe.\nCampur bumbu dengan tahu, tempe, dan cabe rawit.\nBungkus dengan daun salam, kukus 30 menit hingga matang.",
                'ingredients' => [
                    ['Tahu Putih', 100, 'gram'],
                    ['Tempe', 100, 'gram'],
                    ['Bawang Merah', 3, 'gram'],
                    ['Bawang Putih', 2, 'gram'],
                    ['Kemiri', 2, 'butir'],
                    ['Cabe Rawit', 2, 'gram'],
                    ['Daun Salam', 1, 'lembar'],
                    ['Jahe', 5, 'gram'],
                ],
            ],
            [
                'name' => 'Rendang Daging Sapi',
                'description' => 'Rendang sapi bumbu santan, ikon masakan Indonesia.',
                'cook_time_minutes' => 150,
                'instructions' => "Haluskan bawang merah, bawang putih, cabe, kemiri, jahe, kunyit.\nTumis bumbu halus bersama serai dan daun jeruk hingga harum.\nMasukkan daging sapi, aduk hingga berubah warna.\nTambahkan santan dan gula merah, masak dengan api kecil hingga daging empuk dan kuah menyusut.",
                'ingredients' => [
                    ['Daging Sapi', 500, 'gram'],
                    ['Santan Instan', 400, 'gram'],
                    ['Kemiri', 4, 'butir'],
                    ['Cabe Merah Keriting', 50, 'gram'],
                    ['Bawang Merah', 50, 'gram'],
                    ['Bawang Putih', 25, 'gram'],
                    ['Serai', 2, 'batang'],
                    ['Jahe', 20, 'gram'],
                    ['Kunyit', 10, 'gram'],
                    ['Daun Jeruk', 3, 'lembar'],
                    ['Gula Merah', 20, 'gram'],
                ],
            ],
            [
                'name' => 'Ayam Goreng Bawang Putih',
                'description' => 'Ayam goreng kekuningan dengan aroma bawang putih.',
                'cook_time_minutes' => 45,
                'instructions' => "Marinasi paha ayam dengan bawang putih halus, garam, dan ketumbar selama 30 menit.\nGoreng ayam hingga matang kecokelatan.\nTiriskan dan sajikan hangat.",
                'ingredients' => [
                    ['Paha Ayam', 300, 'gram'],
                    ['Bawang Putih', 5, 'gram'],
                    ['Garam', 1, 'sdt'],
                    ['Ketumbar Bubuk', 1, 'sdm'],
                    ['Minyak Goreng', 2, 'sdm'],
                ],
            ],
            [
                'name' => 'Urap Sayur',
                'description' => 'Sayur rebus dengan kelapa parut berbumbu, khas Jawa.',
                'cook_time_minutes' => 30,
                'instructions' => "Rebus kacang panjang dan bayam hingga matang, tiriskan.\nCampur kelapa parut dengan terasi, gula merah, bawang merah, dan cabe rawit yang sudah dihaluskan.\nAduk sayur dengan kelapa berbumbu hingga rata. Sajikan.",
                'ingredients' => [
                    ['Kacang Panjang', 100, 'gram'],
                    ['Bayam', 100, 'gram'],
                    ['Kelapa Parut', 100, 'gram'],
                    ['Terasi', 0.5, 'sdm'],
                    ['Gula Merah', 15, 'gram'],
                    ['Bawang Merah', 3, 'gram'],
                    ['Cabe Rawit', 3, 'gram'],
                ],
            ],
            [
                'name' => 'Nasi Goreng Sederhana',
                'description' => 'Nasi goreng telur rumahan yang cepat dan mengenyangkan.',
                'cook_time_minutes' => 15,
                'instructions' => "Tumis bawang merah dan bawang putih hingga harum.\nMasukkan telur, orak-arik hingga matang.\nMasukkan nasi putih dan kecap manis, aduk rata.\nTambahkan daun bawang, masak sebentar. Sajikan.",
                'ingredients' => [
                    ['Nasi Putih', 200, 'gram'],
                    ['Telur Ayam', 1, 'butir'],
                    ['Bawang Merah', 3, 'gram'],
                    ['Bawang Putih', 2, 'gram'],
                    ['Kecap Manis', 1, 'sdm'],
                    ['Daun Bawang', 5, 'gram'],
                    ['Minyak Goreng', 1, 'sdm'],
                ],
            ],
            [
                'name' => 'Tumis Tahu Tempe Kecap',
                'description' => 'Paduan tahu tempe dengan kecap manis, protein nabati utama.',
                'cook_time_minutes' => 20,
                'instructions' => "Goreng tahu dan tempe setengah matang, sisihkan.\nTumis bawang merah, bawang putih, dan cabe hingga harum.\nMasukkan tahu tempe, tambahkan kecap manis dan garam.\nAduk rata, masak sebentar. Sajikan.",
                'ingredients' => [
                    ['Tahu Putih', 100, 'gram'],
                    ['Tempe', 100, 'gram'],
                    ['Kecap Manis', 1, 'sdm'],
                    ['Bawang Merah', 3, 'gram'],
                    ['Bawang Putih', 2, 'gram'],
                    ['Cabe Rawit', 2, 'gram'],
                    ['Garam', 0.5, 'sdt'],
                    ['Minyak Goreng', 1, 'sdm'],
                ],
            ],
            [
                'name' => 'Bubur Ayam',
                'description' => 'Bubur nasi dengan suwiran ayam, hangat untuk sarapan anak.',
                'cook_time_minutes' => 30,
                'instructions' => "Rebus dada ayam dengan jahe dan garam hingga matang, suwir.\nCampur nasi putih dengan kaldu, masak hingga menjadi bubur.\nSajikan bubur dengan suwiran ayam, daun bawang, dan bawang putih goreng.",
                'ingredients' => [
                    ['Nasi Putih', 200, 'gram'],
                    ['Dada Ayam', 100, 'gram'],
                    ['Jahe', 10, 'gram'],
                    ['Bawang Putih', 3, 'gram'],
                    ['Daun Bawang', 5, 'gram'],
                    ['Garam', 0.5, 'sdt'],
                ],
            ],
            [
                'name' => 'Pisang Goreng',
                'description' => 'Pisang goreng tepung manis, camilan keluarga yang disukai anak.',
                'cook_time_minutes' => 15,
                'instructions' => "Campur tepung terigu, gula pasir, dan air hingga menjadi adonan kental.\nCelupkan pisang ke dalam adonan.\nGoreng hingga kuning keemasan. Angkat dan tiriskan.",
                'ingredients' => [
                    ['Pisang', 3, 'buah'],
                    ['Tepung Terigu', 100, 'gram'],
                    ['Gula Pasir', 30, 'gram'],
                    ['Minyak Goreng', 2, 'sdm'],
                ],
            ],
            [
                'name' => 'Sayur Sop Daging',
                'description' => 'Sop daging sapi dengan aneka sayur, merupakan menu utama saat anak sakit.',
                'cook_time_minutes' => 60,
                'instructions' => "Rebus daging sapi hingga empuk, buang busa.\nMasukkan wortel dan kentang, masak hingga matang.\nTambahkan kacang panjang, bawang merah goreng, dan seledri.\nBeri garam, sajikan panas.",
                'ingredients' => [
                    ['Daging Sapi', 250, 'gram'],
                    ['Wortel', 100, 'gram'],
                    ['Kentang', 100, 'gram'],
                    ['Kacang Panjang', 50, 'gram'],
                    ['Bawang Merah', 3, 'gram'],
                    ['Bawang Putih', 2, 'gram'],
                    ['Seledri', 5, 'gram'],
                    ['Garam', 1, 'sdt'],
                ],
            ],
        ];

        foreach ($recipes as $recipe) {
            DB::table('recipes')->updateOrInsert(
                ['name' => $recipe['name']],
                [
                    'name' => $recipe['name'],
                    'description' => $recipe['description'],
                    'instructions' => $recipe['instructions'],
                    'cook_time_minutes' => $recipe['cook_time_minutes'],
                    'source' => 'manual',
                    'created_by' => null,
                ],
            );

            $recipeId = DB::table('recipes')->where('name', $recipe['name'])->value('id');

            foreach ($recipe['ingredients'] as [$ingredientName, $quantity, $unit]) {
                $ingredientId = $ingredients[$ingredientName] ?? null;

                if (! $ingredientId) {
                    continue;
                }

                DB::table('recipe_ingredients')->updateOrInsert(
                    ['recipe_id' => $recipeId, 'ingredient_id' => $ingredientId],
                    ['quantity_needed' => $quantity, 'unit' => $unit],
                );
            }
        }
    }
}
