<?php

namespace Database\Seeders;

use App\Models\AfugentFaunaFormaRegistroModel;
use Illuminate\Database\Seeder;

class FormaRegistroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formas = [
            ['nome' => 'Afugentamento'],
            ['nome' => 'Resgate'],
            ['nome' => 'Remoção'],
            ['nome' => 'Isolamento']
        ];

        AfugentFaunaFormaRegistroModel::insert($formas);
    }
}
