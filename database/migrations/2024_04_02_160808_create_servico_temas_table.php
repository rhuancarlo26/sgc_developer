<?php

use App\Models\ServicoTema;
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
        Schema::create('servico_temas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->timestamps();
        });

        $now = Carbon::now();

        $temas = [
            [
                "nome" => "Fauna",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "nome" => "Flora",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "nome" => "Recursos Hídricos",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "nome" => "Bens Culturais Acautelados",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "nome" => "Espeleologia",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "nome" => "Indígena",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "nome" => "Quilombola",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "nome" => "Saúde",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "nome" => "PAC",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "nome" => "Sociais",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "nome" => "Físicos",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "nome" => "PRAD",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "nome" => "Outros",
                "created_at" => $now,
                "updated_at" => $now
            ],
            [
                "nome" => "Supervisão Ambiental",
                "created_at" => $now,
                "updated_at" => $now
            ]
        ];

        ServicoTema::insert($temas);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_temas');
    }
};
