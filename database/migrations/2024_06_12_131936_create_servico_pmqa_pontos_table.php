<?php

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
        Schema::create('servico_pmqa_pontos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servico_id')->constrained('servicos')->cascadeOnDelete();
            $table->string('nomepontocoleta');
            $table->double('lat_x');
            $table->double('long_y');
            $table->string('classificacao');
            $table->integer('classe');
            $table->string('tipoambiente');
            $table->string('uf');
            $table->string('municipio');
            $table->string('baciahidrografica');
            $table->integer('km_rodovia');
            $table->integer('estaca');
            $table->text('observacoes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_pmqa_pontos');
    }
};
