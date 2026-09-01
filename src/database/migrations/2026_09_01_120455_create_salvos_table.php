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
        Schema::create('salvos', function (Blueprint $table) {
            $table->id('id_salvo');
            $table->foreignId('fk_id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->foreignId('fk_id_publicacao')->constrained('publicacoes', 'id_publicacao')->onDelete('cascade');
            $table->timestamps();

            // Evita que o usuário salve a mesma publicação mais de uma vez
            $table->unique(['fk_id_usuario', 'fk_id_publicacao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salvos');
    }
};
