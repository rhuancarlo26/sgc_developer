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
        Schema::create('afugent_fauna_exec_registro_imagem', function (Blueprint $table) {
            $table->id();
            $table->string('chave', 255);
            $table->integer('id_registro')->nullable();
            $table->text('nome')->nullable();
            $table->text('caminho_imagem')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afugent_fauna_exec_registro_imagem');
    }
};
