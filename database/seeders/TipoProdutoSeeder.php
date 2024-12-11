<?php

namespace Database\Seeders;

use App\Models\TipoProduto;
use Illuminate\Database\Seeder;

class TipoProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoProduto::query()->insert([
            [
                'chave' => '897b980b980ff7094d72d0505ec74689',
                'nome' => 'Matéria Orgânica',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'chave' => '52b318703eb04b1e79c4e9f4dcedfce5',
                'nome' => 'Torete Lenha',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'chave' => '9dc03f7e97e894b5eaed73e678861670',
                'nome' => 'Tora Comercial',
                'created_at' => '2023-02-10 14:09:00'
            ],
        ]);
    }
}

