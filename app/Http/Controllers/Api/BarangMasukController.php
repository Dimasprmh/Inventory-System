<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\HistoriBarang;

class BarangMasukController extends Controller
{
    public function index()
    {
        $histori = HistoriBarang::with('item')->where('tipe', 'masuk')->latest()->get();
        return response()->json($histori);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'tanggal' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $item = Item::findOrFail($request->item_id);
        $item->stock += $request->jumlah;
        $item->save();

        HistoriBarang::create([
            'item_id' => $item->id,
            'tipe' => 'masuk',
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan ?? 'Barang masuk via API',
        ]);

        return response()->json(['message' => 'Barang masuk berhasil disimpan'], 201);
    }
}

