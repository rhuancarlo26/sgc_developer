<?php

namespace Database\Seeders;

use App\Models\TipoBioma;
use Illuminate\Database\Seeder;

class TipoBiomaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoBioma::query()->insert([
            [
                'chave' => 'd49f44e8a05c831d02b9a6ee3bb94ae9',
                'nome' => 'Amazônia',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'chave' => 'd0ce8d9c0917fd344226736f320a8c5e',
                'nome' => 'Cerrado',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'chave' => 'ccb9003609ddd80fb17bc67c8f6d5a54',
                'nome' => 'Mata Atlântica',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'chave' => 'ccb9003609ddd80fb17bc67c8f6d5a55',
                'nome' => 'Caatinga',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'chave' => 'ccb9003609ddd80fb17bc67c8f6d5a56',
                'nome' => 'Pampa',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'chave' => 'ccb9003609ddd80fb17bc67c8f6d5a57',
                'nome' => 'Pantanal',
                'created_at' => '2023-02-10 14:09:00'
            ],
        ]);
    }
}
