<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('supervisao_exec_aca', function (Blueprint $table) {
            $table->id();
            $table->string('id_servico');
            $table->string('num_por_servico');
            $table->string('nome_id');
            $table->string('data_aca');
            $table->string('id_lote');
            $table->string('enviado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisao_exec_aca');
    }
};
