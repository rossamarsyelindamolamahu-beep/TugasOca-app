<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    /**
     * Seed sample medicines and their initial stock movements.
     */
    public function run(): void
    {
        $medicines = [
            ['name' => 'Paracetamol', 'category' => 'Analgesik', 'stock' => 50, 'unit' => 'Tablet', 'price' => 5000, 'expiry' => 14],
            ['name' => 'Amoxicillin', 'category' => 'Antibiotik', 'stock' => 30, 'unit' => 'Kapsul', 'price' => 10000, 'expiry' => 10],
            ['name' => 'Antasida', 'category' => 'Obat Lambung', 'stock' => 25, 'unit' => 'Tablet', 'price' => 4000, 'expiry' => 18],
            ['name' => 'Vitamin C 100mg', 'category' => 'Vitamin', 'stock' => 100, 'unit' => 'Tablet', 'price' => 2500, 'expiry' => 12],
            ['name' => 'Ibuprofen', 'category' => 'Analgesik', 'stock' => 10, 'unit' => 'Tablet', 'price' => 7000, 'expiry' => 9],
            ['name' => 'Obat Batuk Sirup', 'category' => 'Obat Batuk', 'stock' => 8, 'unit' => 'Botol', 'price' => 15000, 'expiry' => 7],
            ['name' => 'CTM', 'category' => 'Obat Flu', 'stock' => 6, 'unit' => 'Tablet', 'price' => 3000, 'expiry' => 11],
            ['name' => 'Omeprazole', 'category' => 'Obat Lambung', 'stock' => 40, 'unit' => 'Kapsul', 'price' => 8000, 'expiry' => 16],
            ['name' => 'Antangin', 'category' => 'Obat lainnya', 'stock' => 15, 'unit' => 'Sachet', 'price' => 3000, 'expiry' => 13],
        ];

        foreach ($medicines as $item) {
            $category = Category::where('name', $item['category'])->first();

            if (! $category) {
                continue;
            }

            $medicine = Medicine::create([
                'category_id' => $category->id,
                'name' => $item['name'],
                'stock' => $item['stock'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'expiration_date' => now()->addMonths($item['expiry'])->format('Y-m-d'),
                'description' => "{$item['name']} merupakan obat kategori {$item['category']}.",
            ]);

            $medicine->movements()->create([
                'type' => StockMovement::TYPE_IN,
                'quantity' => $item['stock'],
                'description' => 'Stok awal',
            ]);
        }
    }
}
