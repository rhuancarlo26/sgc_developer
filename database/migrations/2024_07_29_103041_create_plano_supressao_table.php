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
        Schema::create('plano_supressao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servico_id')->constrained('servicos');
            $table->string('arquivo_id');
            $table->string('chave');
            $table->decimal('area_em_app');
            $table->decimal('area_fora_app');
            $table->date('dt_final');
            $table->date('dt_inicial');
            $table->text('local_shape_em_app');
            $table->text('local_shape_fora_app');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plano_supressao');
    }
};
