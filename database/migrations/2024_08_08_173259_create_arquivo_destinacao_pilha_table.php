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
        Schema::create('arquivo_destinacao_pilha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arquivo_id')->constrained('arquivos');
            $table->foreignId('destinacaos_id')->constrained('destinacaos');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arquivo_destinacao_pilha');
    }
};
