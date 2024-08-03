<?php

namespace Database\Seeders;

use App\Models\EstagioSucessional;
use Illuminate\Database\Seeder;

class EstagioSucessionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EstagioSucessional::query()->insert([
            [
                'chave' => '1362565737e2fe98ec287983e32c33b5',
                'nome' => 'Climáxica',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'chave' => '7bdbb8c147b84749b86af29763dcc87f',
                'nome' => 'Secundária Inicial',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'chave' => '5340cbafd624877249997ff42bf95b0a',
                'nome' => 'Secundária Tardia',
                'created_at' => '2023-02-10 14:09:00'
            ],
            [
                'chave' => '4bf22034e62aa7a95b71d8165f00ba4e',
                'nome' => 'Pioneira',
                'created_at' => '2023-02-10 14:09:00'
            ],
        ]);
    }
}
