<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\HistoriBarang;

class BarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = HistoriBarang::with('item')->where('tipe', 'masuk');

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('sku')) {
            $query->where('item_id', $request->sku);
        }

        $perPage = $request->get('per_page', 10);

        // ✅ Urutkan berdasarkan tanggal DESC (terbaru ke terlama)
        return response()->json(
            $query->orderBy('tanggal', 'desc')->paginate($perPage)
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id'    => 'required|exists:items,id',
            'tanggal'    => 'required|date',
            'jumlah'     => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $item = Item::findOrFail($request->item_id);
        $item->stock += $request->jumlah;
        $item->save();

        HistoriBarang::create([
            'item_id'    => $item->id,
            'tipe'       => 'masuk',
            'jumlah'     => $request->jumlah,
            'tanggal'    => $request->tanggal,
            'keterangan' => $request->keterangan ?? 'Barang masuk via API',
        ]);

        return response()->json(['message' => 'Barang masuk berhasil disimpan'], 201);
    }
}
