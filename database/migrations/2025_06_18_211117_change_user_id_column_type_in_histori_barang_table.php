<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kalau sebelumnya sudah ada user_id yang salah tipe, hapus dulu
        Schema::table('histori_barang', function (Blueprint $table) {
            if (Schema::hasColumn('histori_barang', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });

        // Tambahkan ulang dengan tipe UUID (char 36) dan nullable
        Schema::table('histori_barang', function (Blueprint $table) {
            $table->char('user_id', 36)->nullable()->after('item_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    public function down(): void
    {
        // Rollback: hapus foreign key dan kolom user_id UUID
        Schema::table('histori_barang', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        // Tambahkan kembali versi sebelumnya jika ingin rollback (opsional)
        Schema::table('histori_barang', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('item_id');
        });
    }
};
