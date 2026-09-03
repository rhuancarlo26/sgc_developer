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
      Schema::create('sgc_patrimonio_shapefile', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('patrimonio_paipa_id');
        $table->string('nome_campo');
        $table->json('geo_json');

        $table->timestamps();

        $table->foreign('patrimonio_paipa_id')
          ->references('id')
          ->on('sgc_patrimonio_paipa')
          ->cascadeOnDelete();

        $table->unique(['patrimonio_paipa_id', 'nome_campo']);
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sgc_patrimonio_shapefile');
    }
};
