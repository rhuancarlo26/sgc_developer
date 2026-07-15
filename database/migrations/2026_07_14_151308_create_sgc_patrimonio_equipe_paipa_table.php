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
      Schema::create('sgc_patrimonio_equipe_paipa', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('equipe_id');
        $table->unsignedBigInteger('paipa_id');
        $table->string('tipo_participacao')->nullable();
        $table->date('data_inicio')->nullable();
        $table->date('data_fim')->nullable();
        $table->boolean('ativo')->default(true);
        $table->timestamps();

        $table->foreign('equipe_id')->references('id')->on('sgc_patrimonio_equipe')->cascadeOnDelete();
        $table->foreign('paipa_id')->references('id')->on('sgc_patrimonio_paipa')->cascadeOnDelete();

        $table->unique(['equipe_id', 'paipa_id']);
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sgc_patrimonio_equipe_paipa');
    }
};
