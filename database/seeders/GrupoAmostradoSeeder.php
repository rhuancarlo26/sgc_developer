<?php

namespace Database\Seeders;

use App\Models\AtFaunaGrupoAmostradoModel;
use Illuminate\Database\Seeder;

class GrupoAmostradoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grupos = [
            ['nome' => 'Avifauna'],
            ['nome' => 'Herpetofauna'],
            ['nome' => 'Mastofauna'],
        ];

        AtFaunaGrupoAmostradoModel::insert($grupos);
    }
}
