<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sgc_modulos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('nome_planilha_modelo')->nullable();
            $table->string('caminho_planilha_modelo')->nullable();
            $table->json('campos');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgc_modulos');
    }
};
