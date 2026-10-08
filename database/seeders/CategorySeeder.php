<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the default medicine categories.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Analgesik', 'description' => 'Obat pereda nyeri dan penurun panas'],
            ['name' => 'Antibiotik', 'description' => 'Obat antibakteri untuk mengatasi infeksi'],
            ['name' => 'Vitamin', 'description' => 'Suplemen vitamin dan mineral'],
            ['name' => 'Obat Lambung', 'description' => 'Obat gangguan sistem pencernaan'],
            ['name' => 'Obat Batuk', 'description' => 'Obat pereda batuk'],
            ['name' => 'Obat Flu', 'description' => 'Obat gejala influenza dan alergi'],
            ['name' => 'Obat lainnya', 'description' => 'Kategori obat lainnya'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
