<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HistoriBarang;
use App\Models\Product;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Grafik Barang Masuk & Keluar per Bulan
    public function grafikBarangBulanan()
    {
        $tahun = Carbon::now()->year;

        $data = HistoriBarang::select(
                DB::raw('MONTH(tanggal) as bulan'),
                DB::raw("SUM(CASE WHEN tipe = 'masuk' THEN jumlah ELSE 0 END) as masuk"),
                DB::raw("SUM(CASE WHEN tipe = 'keluar' THEN jumlah ELSE 0 END) as keluar")
            )
            ->whereYear('tanggal', $tahun)
            ->groupBy(DB::raw('MONTH(tanggal)'))
            ->orderBy(DB::raw('MONTH(tanggal)'))
            ->get();

        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulanData = $data->firstWhere('bulan', $i);
            $result[] = [
                'bulan' => Carbon::create()->month($i)->locale('id')->isoFormat('MMMM'),
                'masuk' => $bulanData->masuk ?? 0,
                'keluar' => $bulanData->keluar ?? 0,
            ];
        }

        return response()->json($result);
    }

    // Statistik Total untuk Card di Dashboard
    public function statistik()
    {
        $totalProducts = Product::count();
        $totalItems = Item::count();
        $totalMasuk = HistoriBarang::where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = HistoriBarang::where('tipe', 'keluar')->sum('jumlah');

        return response()->json([
            'products' => $totalProducts,
            'items' => $totalItems,
            'masuk' => $totalMasuk,
            'keluar' => $totalKeluar,
        ]);
    }
}