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
        Schema::create('recurso_veiculo_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recurso_veiculo_id')->constrained('recurso_veiculos', 'id')->cascadeOnDelete();
            $table->string('nome');
            $table->string('caminho');
            $table->string('tipo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurso_veiculo_documentos');
    }
};
