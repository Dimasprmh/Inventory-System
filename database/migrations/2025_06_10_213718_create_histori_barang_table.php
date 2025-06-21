<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriBarangTable extends Migration
{
    public function up()
    {
        Schema::create('histori_barang', function (Blueprint $table) {
            $table->id();
            $table->uuid('item_id');
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->integer('jumlah');
            $table->dateTime('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('histori_barang');
    }
}
