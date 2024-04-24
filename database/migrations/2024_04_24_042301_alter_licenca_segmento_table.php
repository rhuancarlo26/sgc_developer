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
        Schema::dropIfExists('licenca_segmento');

        Schema::create('licenca_segmento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licenca_id')->constrained('licenca', 'id')->cascadeOnDelete();
            $table->foreignId('uf_inicial_id')->constrained('ufs', 'id')->cascadeOnDelete();
            $table->foreignId('uf_final_id')->constrained('ufs', 'id')->cascadeOnDelete();
            $table->foreignId('rodovia_id')->constrained('rodovias', 'id')->cascadeOnDelete();
            $table->float('km_inicial');
            $table->float('km_final');
            $table->float('extensao');
            $table->char('trecho_tipo', 1)->default('B');
            $table->json('coordenada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
