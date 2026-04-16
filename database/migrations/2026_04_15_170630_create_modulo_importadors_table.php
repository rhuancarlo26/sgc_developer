<?php

use App\Models\Contrato;
use App\Models\Modulo;
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
        Schema::create('modulo_importadores', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Modulo::class, 'modulo_id')->constrained('modulos')->cascadeOnDelete();
            $table->string('mes_ano_referencia');
            $table->integer('campanha');

            // 🔧 CORREÇÃO AQUI
            $table->integer('contrato_id');
            $table->foreign('contrato_id')->references('id')->on('contratos')->cascadeOnDelete();
            // $table->foreignIdFor(Contrato::class, 'contrato_id')->constrained('contratos')->cascadeOnDelete();

            $table->string('nome_arquivo');
            $table->integer('status');

            $table->integer('load')->default(1);
            $table->json('desc_erros')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modulo_importadores');
    }
};
