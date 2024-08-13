<?php

namespace Database\Seeders;

use App\Models\TipoPatio;
use Illuminate\Database\Seeder;

class TipoPatioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoPatio::query()->insert([
            [
                'id' => 1,
                'chave' => 'd49f44e8a05c831d02b9a6ee3bb94ae9',
                'nome' => 'Madeira Torete Lenha',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'id' => 2,
                'chave' => 'd0ce8d9c0917fd344226736f320a8c5e',
                'nome' => 'Madeira Tora Comercial',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'id' => 3,
                'chave' => 'ccb9003609ddd80fb17bc67c8f6d5a54',
                'nome' => 'Matéria Orgânica',
                'created_at' => '2023-02-10 14:09:00'
            ],
        ]);
    }
}
