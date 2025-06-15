<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id(); // ID token
            $table->uuidMorphs('tokenable'); // Mendukung UUID untuk user.id
            $table->string('name'); // Nama token
            $table->string('token', 64)->unique(); // Token value
            $table->text('abilities')->nullable(); // Hak akses
            $table->timestamp('last_used_at')->nullable(); // Terakhir dipakai
            $table->timestamp('expires_at')->nullable(); // Kedaluwarsa
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
