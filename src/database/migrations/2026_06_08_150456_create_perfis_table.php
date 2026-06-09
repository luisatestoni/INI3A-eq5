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
        Schema::create('perfis', function (Blueprint $table) {
            $table->id('id_perfil');
            $table->foreignId('fk_id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->string('foto')->nullable();
            $table->string('tipo')->nullable();
            $table->timestamp('data_atualizacao')->useCurrent();
            $table->string('capa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfis');
    }
};
