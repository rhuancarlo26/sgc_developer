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
        Schema::create('area_supressao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servico_id')->constrained('servicos');
            $table->foreignId('licenca_id')->constrained('licenca');
            $table->foreignId('estagio_sucessional_id')->constrained('estagio_sucessional');
            $table->foreignId('tipo_bioma_id')->constrained('tipo_biomas');
            $table->string('chave')->nullable();
            $table->dateTime('dt_inicial');
            $table->dateTime('dt_final');
            $table->text('fitofisionomia')->nullable();
            $table->decimal('area_em_app');
            $table->decimal('area_fora_app');
            $table->decimal('area_total');
            $table->string('corte_especie')->nullable();
            $table->text('observacao')->nullable();
            $table->longText('shapefile')->nullable();
            $table->string('local_shape')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_supressao');
    }
};
