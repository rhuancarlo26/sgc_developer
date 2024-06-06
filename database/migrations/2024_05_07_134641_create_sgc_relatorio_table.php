<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSgcRelatorioTable extends Migration
{
    public function up()
{
    Schema::create('sgc_relatorio_coordenacao', function (Blueprint $table) {
        $table->id();
        $table->integer('relatorio_num');
        $table->integer('id_item')->unique();
        $table->string('nome_topico');
        $table->integer('status');
        $table->integer('aprovado');
        $table->integer('imagem');
        $table->string('texto', 9999);
        $table->timestamps();
        $table->softDeletes();
        $table->string('data_atualizacao', 45);
    });

}


    public function down()
    {
        Schema::dropIfExists('sgc_relatorio_coordenacao');
    }

}

