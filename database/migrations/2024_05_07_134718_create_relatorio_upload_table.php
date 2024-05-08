<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelatorioUploadTable extends Migration
{
    public function up()
    {
        Schema::create('relatorio_upload', function (Blueprint $table) {
            $table->id();
            $table->integer('contract_id');
            $table->string('nome');
            $table->unsignedBigInteger('item_id');
            $table->string('caminho');
            $table->string('tipo');
            $table->string('descricao');
            $table->integer('versao');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('relatorio_upload');
    }
}
