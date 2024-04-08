<?php

use App\Models\ServicoTipo;
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
        Schema::create('servico_tipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servico_tema_id')->constrained('servico_temas')->cascadeOnDelete();
            $table->string('nome');
            $table->timestamps();
        });

        $now = Carbon::now();

        $tipos = [
            [
                "servico_tema_id" => 3,
                "nome" => "Monitoramento da Qualidade da Água",
                "created_at" => $now,
                "updated_at" => $now,
            ],
            [
                "servico_tema_id" => 1,
                "nome" => "Afugentamento e Resgate de Fauna",
                "created_at" => $now,
                "updated_at" => $now,
            ],
            [
                "servico_tema_id" => 1,
                "nome" => "Monitoramento do Atropelamento da Fauna",
                "created_at" => $now,
                "updated_at" => $now,
            ],
            [
                "servico_tema_id" => 1,
                "nome" => "Monitoramento da Fauna",
                "created_at" => $now,
                "updated_at" => $now,
            ],
            [
                "servico_tema_id" => 1,
                "nome" => "Monitoramento de Passagem da Fauna",
                "created_at" => $now,
                "updated_at" => $now,
            ],
            [
                "servico_tema_id" => 2,
                "nome" => "Supressão de Vegetação",
                "created_at" => $now,
                "updated_at" => $now,
            ],
            [
                "servico_tema_id" => 14,
                "nome" => "Controle de Ocorrências",
                "created_at" => $now,
                "updated_at" => $now,
            ]
        ];

        ServicoTipo::insert($tipos);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_tipos');
    }
};