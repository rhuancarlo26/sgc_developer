<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('sgc_espeleo_profissionais')) {
            return;
        }

        Schema::create('sgc_espeleo_profissionais', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_contrato')->nullable();
            $table->string('profissional')->nullable();
            $table->string('formacao')->nullable();
            $table->text('telefone')->nullable();
            $table->text('cpf')->nullable();
            $table->text('email')->nullable();
            $table->text('curriculum_lattes')->nullable();
            $table->text('funcao')->nullable();
            $table->text('ctf')->nullable();
            $table->date('validade')->nullable();
            $table->text('conselho_de_classe')->nullable();
            $table->integer('numero_de_registro')->nullable();
            $table->text('status')->nullable();
            $table->text('observacao')->nullable();
            $table->date('created_at')->nullable();
            $table->date('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgc_espeleo_profissionais');
    }
};
