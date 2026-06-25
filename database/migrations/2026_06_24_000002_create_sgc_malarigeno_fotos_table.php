<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sgc_malarigeno_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sgc_malarigeno_id')
                ->constrained('sgc_malarigeno')
                ->cascadeOnDelete();
            $table->string('nome_arquivo')->nullable();
            $table->string('caminho_arquivo')->nullable();
            $table->decimal('latitude', 11, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->dateTime('data_captura')->nullable();
            $table->text('descricao')->nullable();
            $table->json('metadados')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgc_malarigeno_fotos');
    }
};
