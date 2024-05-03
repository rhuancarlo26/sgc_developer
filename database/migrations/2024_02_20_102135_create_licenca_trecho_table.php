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
        Schema::create('licenca_trechos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licenca_id')->constrained('licenca', 'id')->cascadeOnDelete();
            $table->foreignId('uf_id')->constrained('ufs', 'id')->cascadeOnDelete();
            $table->foreignId('rodovia_id')->constrained('rodovias', 'id')->cascadeOnDelete();
            $table->float('km_inicial');
            $table->float('km_final');
            $table->char('trecho_tipo', 1);
            $table->json('coordenada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenca_trechos');
    }
};
