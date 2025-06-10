<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua ID dari tabel products
        $productIds = DB::table('products')->pluck('id');

        if ($productIds->isEmpty()) {
            echo "Tabel 'products' kosong. Silakan isi terlebih dahulu.\n";
            return;
        }

        // Data varian gelas
        $items = [
            ['sku' => 'SKU-2001', 'ukuran' => 250, 'stock' => 100, 'merk' => 'Tupperware'],
            ['sku' => 'SKU-2002', 'ukuran' => 200, 'stock' => 100, 'merk' => 'Tupperware'],
            ['sku' => 'SKU-2003', 'ukuran' => 300, 'stock' => 120, 'merk' => 'Lock&Lock'],
            ['sku' => 'SKU-2004', 'ukuran' => 200, 'stock' => 90,  'merk' => 'Polytron'],
            ['sku' => 'SKU-2005', 'ukuran' => 350, 'stock' => 70,  'merk' => 'Ecentio'],
            ['sku' => 'SKU-2006', 'ukuran' => 400, 'stock' => 110, 'merk' => 'Krisbow'],
        ];

        foreach ($items as $item) {
            DB::table('items')->insert([
                'id' => (string) Str::uuid(),
                'ukuran' => $item['ukuran'],
                'stock' => $item['stock'],
                'merk' => $item['merk'],
                'sku' => $item['sku'],
                'product_id' => $productIds->random(), // ambil satu product_id secara acak
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
