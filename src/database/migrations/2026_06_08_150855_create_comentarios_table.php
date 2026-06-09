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
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id('id_comentario');
            $table->foreignId('fk_id_post')->constrained('publicacoes', 'id_publicacao')->onDelete('cascade');
            $table->foreignId('fk_id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->text('conteudo');
            $table->unsignedBigInteger('id_pai')->nullable(); // Auto-relacionamento para respostas
            $table->timestamp('data_comentario')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
