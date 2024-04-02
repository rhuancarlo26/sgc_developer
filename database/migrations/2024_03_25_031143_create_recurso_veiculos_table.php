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
        Schema::create('recurso_veiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos', 'id')->cascadeOnDelete();
            $table->foreignId('veiculo_codigo_id')->constrained('recurso_veiculo_codigos', 'id')->cascadeOnDelete();
            $table->text('observacao');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurso_veiculos');
    }
};
