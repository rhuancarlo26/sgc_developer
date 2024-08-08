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
        Schema::create('patio_estocagem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servico_id')->constrained('servicos');
            $table->foreignId('licenca_id')->constrained('licenca');
            $table->foreignId('tipo_patio_id')->constrained('tipo_patio');
            $table->string('chave');
            $table->datetime('dt_cadastro')->nullable();
            $table->longText('shapefile')->nullable();
            $table->text('observacao')->nullable();
            $table->string('local_shape')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patio_estocagem');
    }
};
