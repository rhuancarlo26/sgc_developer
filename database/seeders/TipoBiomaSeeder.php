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
                'chave' => 'bc4801e49a89d60e33a6cfe74c2ff01e',
                'nome' => 'Amazônia',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'chave' => '553f55540a1e4d55827c97894fdb1df4',
                'nome' => 'Cerrado',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'chave' => '24411de03b918e3a290ef5b329f09bc5',
                'nome' => 'Mata Atlântica',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'chave' => '6a344f81a1cedf5e5f8939f257101107',
                'nome' => 'Caatinga',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'chave' => '2667996a5c2528e4e23961fcb2962f3e',
                'nome' => 'Pampa',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'chave' => 'ebb2b0444d8fe26a361bd4bbdebe5723',
                'nome' => 'Pantanal',
                'created_at' => '2023-02-10 14:09:00'
            ],
        ]);
    }
}
