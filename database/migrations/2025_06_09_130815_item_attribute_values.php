<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('item_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->uuid('item_id');
            $table->unsignedBigInteger('product_attribute_id');
            $table->string('value'); // contoh: Merah, Plastik, dll
            $table->timestamps();

            // Relasi ke tabel items dan product_attributes
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('product_attribute_id')->references('id')->on('product_attributes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_attribute_values');
    }
};
