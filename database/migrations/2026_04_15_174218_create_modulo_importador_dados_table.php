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
        Schema::create('modulo_importador_dados', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ModuloImportador::class, 'modulo_importador_id')->constrained('modulo_importadores')->cascadeOnDelete();
            $table->json('dados');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modulo_importador_dados');
    }
};
