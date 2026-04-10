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
        // para rodar isoladamente
        // php artisan migrate --path='database/migrations/2026_04_08_142005_create_modulos_table.php'
        Schema::create('modulos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('nome_planilha_modelo')->nullable();
            $table->string('caminho_planilha_modelo')->nullable();
            $table->json('campos');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modulos');
    }
};
