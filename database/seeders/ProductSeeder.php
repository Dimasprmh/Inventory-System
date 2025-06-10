<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Gelas', 'unit' => 'box'],
            ['name' => 'Botol', 'unit' => 'pack'],
            ['name' => 'Sterefoam', 'unit' => 'pack'],
            ['name' => 'Kantong Plastik', 'unit' => 'karung'],
            ['name' => 'Kotak Makanan Plastik', 'unit' => 'box'],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert([
                'id' => (string) Str::uuid(),
                'name' => $product['name'],
                'unit' => $product['unit'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
