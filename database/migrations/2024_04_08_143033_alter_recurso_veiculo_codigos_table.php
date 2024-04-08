<?php

use App\Models\RecursoVeiculoCodigo;
use Carbon\Carbon;
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
        $now = Carbon::now();

        $codigo_veiculos = [
            [
                "codigo" => "E9093",
                "descricao" => "Veículo leve - 53 kW (sem motorista)",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "codigo" => "E9512",
                "descricao" => "Veículo leve - 53 kW",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "codigo" => "E9684",
                "descricao" => "Veículo leve picape 4 x 4 com capacidade de 1,10 t - 147 kW",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "codigo" => "E9125",
                "descricao" => "Veículo tipo van furgão com capacidade de 1,54 t - 93 kW",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "codigo" => "E8889",
                "descricao" => "Veículo leve - 53 kW (sem motorista)",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "codigo" => "E8891",
                "descricao" => "Veículo leve picape 4x4 - 147 kW (sem motorista)",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "codigo" => "E8887",
                "descricao" => "Van furgão - 93 kW (com motorista)",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "codigo" => "E9536",
                "descricao" => "Embarcação de transporte de pessoal e apoio logístico - 30 kW",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "codigo" => "E9601",
                "descricao" => "Embarcação de transporte de pessoal e apoio logístico - 130 kW",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "codigo" => "E9676",
                "descricao" => "Embarcação de transporte de pessoal e apoio logístico - 90 kW",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "codigo" => "E9043",
                "descricao" => "Embarcação de alumínio com comprimento de 6 m e motor de popa - 18,60 kW",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "codigo" => "E9687",
                "descricao" => "Caminhão carroceria com capacidade de 5 t - 115 kW",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "codigo" => "E9690",
                "descricao" => "Caminhão carroceria com guindauto e cesto aéreo com capacidade de 10 t.m - 136 kW",
                "created_at" => $now,
                "updated_at" => $now
            ]
        ];

        RecursoVeiculoCodigo::insert($codigo_veiculos);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};