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
        Schema::create('corte_especie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_supressao_id')->constrained('area_supressao');
            $table->string('nome');
            $table->string('nome_popular');
            $table->integer('qtd_corte');
            $table->string('compensacao')->nullable();
            $table->text('legislacao')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corte_especie');
    }
};
