<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HistoriBarang;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = HistoriBarang::with('item')
            ->where('user_id', $userId);

        // Filter berdasarkan tipe: 'masuk' atau 'keluar'
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Filter tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        // Ambil hasil dan urutkan berdasarkan tanggal terbaru
        $logs = $query->orderBy('tanggal', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
}
