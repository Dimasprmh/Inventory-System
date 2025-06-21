<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $productId = 'caeb954e-c593-4138-b04a-3d290c60f3e2';

        // Ambil ID dari atribut "Jenis"
        $jenisAttributeId = DB::table('product_attributes')
            ->where('product_id', $productId)
            ->where('name', 'Jenis')
            ->value('id');

        if (!$jenisAttributeId) {
            $this->command->error('Attribute "Jenis" belum ada di product_attributes.');
            return;
        }

        $ukuranList = ['500 ML', '650 ML', '750 ML', '1000 ML'];
        $jenisList = ['Round', 'Rectangle'];

        foreach ($jenisList as $jenis) {
            foreach ($ukuranList as $ukuran) {
                $itemId = (string) Str::uuid();

                DB::table('items')->insert([
                    'id' => $itemId,
                    'sku' => 'TW-' . strtoupper(substr($jenis, 0, 3)) . '-' . str_replace(' ', '', $ukuran),
                    'merk' => 'Thinwall',
                    'ukuran' => $ukuran,
                    'stock' => rand(5, 20),
                    'product_id' => $productId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                DB::table('item_attribute_values')->insert([
                    'item_id' => $itemId,
                    'product_attribute_id' => $jenisAttributeId,
                    'value' => $jenis,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
