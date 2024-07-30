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
        Schema::create('arquivo_patio_estocagem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arquivo_id')->constrained('arquivos');
            $table->foreignId('patio_estocagem_id')->constrained('patio_estocagem');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arquivo_patio_estocagem');
    }
};
