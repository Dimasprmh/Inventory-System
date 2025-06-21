<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GelasProductSeeder extends Seeder
{
    public function run(): void
    {
        $productId = 'fa4d8cda-88e7-41de-a81c-af575966f02b';

        // Tambah attribute 'Jenis' jika belum ada
        $attributeId = DB::table('product_attributes')
            ->where('product_id', $productId)
            ->where('name', 'Jenis')
            ->value('id');

        if (!$attributeId) {
            $attributeId = DB::table('product_attributes')->insertGetId([
                'product_id' => $productId,
                'name' => 'Jenis',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        $data = [
            'AJP' => 'Polos',
            'Merak' => 'Polkadot',
        ];

        $ukuranList = ['08 OZ', '10 OZ', '12 OZ', '14 OZ', '16 OZ'];

        foreach ($data as $merk => $jenis) {
            foreach ($ukuranList as $ukuran) {
                $sku = 'GLS-' . strtoupper(substr($merk, 0, 3)) . '-' . str_replace(' ', '', $ukuran);

                // Cek apakah SKU sudah ada
                if (DB::table('items')->where('sku', $sku)->exists()) {
                    continue;
                }

                $itemId = Str::uuid()->toString();

                DB::table('items')->insert([
                    'id' => $itemId,
                    'sku' => $sku,
                    'merk' => $merk,
                    'ukuran' => $ukuran,
                    'stock' => rand(10, 20),
                    'product_id' => $productId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                DB::table('item_attribute_values')->insert([
                    'item_id' => $itemId,
                    'product_attribute_id' => $attributeId,
                    'value' => $jenis,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
