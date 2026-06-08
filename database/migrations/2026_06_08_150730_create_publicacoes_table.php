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
        Schema::create('publicacoes', function (Blueprint $table) {
            $table->id('id_publicacao');
            $table->foreignId('fk_id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->string('titulo');
            $table->text('resumo')->nullable();
            $table->longText('conteudo');
            $table->string('capa')->nullable();
            $table->string('status')->default('publicado');
            $table->timestamp('data_publicacao')->useCurrent();
            $table->string('categorias')->nullable();
            $table->timestamp('data_atualizacao')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicacoes');
        Schema::table('publicacoes', function (Blueprint $table) {
        $table->dropColumn('categorias');
    });
    }
};
