<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sgc_malarigeno', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_contrato');
            $table->string('subproduto')->nullable();
            $table->unsignedBigInteger('modulo_id')->nullable();
            $table->string('status')->default('Em elaboração');
            $table->string('planilha_nome')->nullable();
            $table->string('planilha_caminho')->nullable();
            $table->timestamps();

            $table->index('id_contrato');
            $table->index('modulo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgc_malarigeno');
    }
};
