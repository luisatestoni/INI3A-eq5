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
        Schema::create('seguidores', function (Blueprint $table) {
            $table->id('id_seguidor');
            // Quem está seguindo
            $table->unsignedBigInteger('fk_id_seguidor'); 
            // Quem está SENDO seguido
            $table->unsignedBigInteger('fk_id_seguido'); 
            $table->timestamps();

            // Chaves estrangeiras apontando para a sua tabela de usuários
            $table->foreign('fk_id_seguidor')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('fk_id_seguido')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            
            // Impede que a mesma pessoa siga o mesmo usuário duas vezes
            $table->unique(['fk_id_seguidor', 'fk_id_seguido']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguidores');
    }
};
