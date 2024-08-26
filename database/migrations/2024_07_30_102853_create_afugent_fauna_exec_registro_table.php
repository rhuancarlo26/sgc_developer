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
        Schema::create('afugent_fauna_exec_registro', function (Blueprint $table) {
            $table->id();
            $table->string('chave', 255);
            $table->integer('id_servico')->nullable();
            $table->integer('id_frente')->nullable();
            $table->integer('id_grupo_amostrado')->nullable();
            $table->datetime('data_registro')->nullable();
            $table->integer('id_estado')->nullable();
            $table->text('nome_registro')->nullable();
            $table->text('km')->nullable();
            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();
            $table->text('zona')->nullable();
            $table->text('sentido')->nullable();
            $table->text('margem')->nullable();
            $table->text('classe')->nullable();
            $table->text('ordem')->nullable();
            $table->text('familia')->nullable();
            $table->text('genero')->nullable();
            $table->text('especie')->nullable();
            $table->text('nome_comum')->nullable();
            $table->text('sexo')->nullable();
            $table->text('faixa_etaria')->nullable();
            $table->text('n_individuos')->nullable();
            $table->text('id_forma_registro')->nullable();
            $table->text('id_tipo_registro')->nullable();
            $table->text('id_destinacao_registro')->nullable();
            $table->text('latitude_soltura')->nullable();
            $table->text('longitude_soltura')->nullable();
            $table->text('zona_soltura')->nullable();
            $table->text('nome_local')->nullable();
            $table->text('coletado')->nullable();
            $table->text('n_registro_tombamento')->nullable();
            $table->integer('id_status_conservacao_federal')->nullable();
            $table->integer('id_status_conservacao_iucn')->nullable();
            $table->text('obs')->nullable();
            $table->time('hora_registro')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afugent_fauna_exec_registro_models');
    }
};
