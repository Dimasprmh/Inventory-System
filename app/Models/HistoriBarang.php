<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriBarang extends Model
{
    protected $table = 'histori_barang';

    protected $fillable = ['item_id', 'tipe', 'jumlah', 'tanggal', 'keterangan'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

