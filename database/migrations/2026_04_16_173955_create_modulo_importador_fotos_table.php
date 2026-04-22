<?php

use App\Models\ModuloImportador;
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
        Schema::create('modulo_importador_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ModuloImportador::class, 'modulo_importador_id')->constrained('modulo_importadores')->cascadeOnDelete();
            $table->string('nome_arquivo');
            $table->string('caminho_arquivo');
            $table->float('latitude');
            $table->float('longitude');
            $table->text('descricao');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modulo_importador_fotos');
    }
};
