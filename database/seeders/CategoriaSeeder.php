<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run()
    {
        $categorias = [
            ['nome' => 'Texto', 'grupo' => 'Formatos'],
            ['nome' => 'Artigo', 'grupo' => 'Formatos'],
            ['nome' => 'Crônica', 'grupo' => 'Formatos'],
            ['nome' => 'Poesia', 'grupo' => 'Formatos'],
            ['nome' => 'Conto', 'grupo' => 'Formatos'],
            ['nome' => 'Resenha', 'grupo' => 'Formatos'],

            ['nome' => 'Tecnologia', 'grupo' => 'Tecnologia & Ciência'],
            ['nome' => 'Programação', 'grupo' => 'Tecnologia & Ciência'],
            ['nome' => 'Inteligência Artificial', 'grupo' => 'Tecnologia & Ciência'],
            ['nome' => 'Ciência', 'grupo' => 'Tecnologia & Ciência'],
            ['nome' => 'Gadgets', 'grupo' => 'Tecnologia & Ciência'],
            ['nome' => 'Games', 'grupo' => 'Tecnologia & Ciência'],

            ['nome' => 'Cinema & TV', 'grupo' => 'Cultura & Entretenimento'],
            ['nome' => 'Literatura', 'grupo' => 'Cultura & Entretenimento'],
            ['nome' => 'Música', 'grupo' => 'Cultura & Entretenimento'],
            ['nome' => 'Arte & Design', 'grupo' => 'Cultura & Entretenimento'],
            ['nome' => 'Pop & Geek', 'grupo' => 'Cultura & Entretenimento'],
            ['nome' => 'História', 'grupo' => 'Cultura & Entretenimento'],

            ['nome' => 'Cotidiano', 'grupo' => 'Estilo de Vida & Cotidiano'],
            ['nome' => 'Reflexões', 'grupo' => 'Estilo de Vida & Cotidiano'],
            ['nome' => 'Saúde & Bem-estar', 'grupo' => 'Estilo de Vida & Cotidiano'],
            ['nome' => 'Viagens', 'grupo' => 'Estilo de Vida & Cotidiano'],
            ['nome' => 'Gastronomia', 'grupo' => 'Estilo de Vida & Cotidiano'],
            ['nome' => 'Esportes', 'grupo' => 'Estilo de Vida & Cotidiano'],

            ['nome' => 'Educação', 'grupo' => 'Carreira & Sociedade'],
            ['nome' => 'Carreira & Negócios', 'grupo' => 'Carreira & Sociedade'],
            ['nome' => 'Finanças', 'grupo' => 'Carreira & Sociedade'],
            ['nome' => 'Produtividade', 'grupo' => 'Carreira & Sociedade'],
            ['nome' => 'Filosofia', 'grupo' => 'Carreira & Sociedade'],
            ['nome' => 'Atualidades', 'grupo' => 'Carreira & Sociedade'],
        ];

        foreach ($categorias as $cat) {
            Categoria::create($cat);
        }
    }
}