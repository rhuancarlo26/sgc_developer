<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('servico_retorno_confeccao_historicos')) {
            return;
        }

        Schema::create('servico_retorno_confeccao_historicos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('servico_id');
            $table->foreignId('contrato_id')->constrained('contratos')->cascadeOnDelete();
            $table->unsignedInteger('tema_servico')->nullable();
            $table->foreignId('servico_mod_imp_id')->nullable()->constrained('modulos')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('status_anterior')->nullable();
            $table->integer('status_novo')->default(1);
            $table->longText('motivo');
            $table->timestamps();
            $table->foreign('servico_id')->references('id')->on('servicos')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servico_retorno_confeccao_historicos');
    }
};
