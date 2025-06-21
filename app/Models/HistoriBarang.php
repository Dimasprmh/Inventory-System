<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriBarang extends Model
{
    protected $table = 'histori_barang';

    protected $fillable = ['item_id', 'tipe', 'jumlah', 'tanggal', 'keterangan', 'user_id'];


    protected $casts = [
        'tanggal' => 'datetime', // agar bisa diformat di backend jika perlu
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
