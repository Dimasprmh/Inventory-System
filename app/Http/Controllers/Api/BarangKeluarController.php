<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\HistoriBarang;

class BarangKeluarController extends Controller
{
    public function index()
    {
        $data = HistoriBarang::with('item')
            ->where('tipe', 'keluar')
            ->latest()
            ->get();

        return response()->json($data);
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
        if ($item->stock < $request->jumlah) {
            return response()->json(['message' => 'Stok tidak mencukupi'], 400);
        }

        $item->stock -= $request->jumlah;
        $item->save();

        HistoriBarang::create([
            'item_id' => $item->id,
            'tipe' => 'keluar',
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan ?? 'Barang keluar via API',
        ]);

        return response()->json(['message' => 'Barang keluar berhasil disimpan'], 201);
    }
}
