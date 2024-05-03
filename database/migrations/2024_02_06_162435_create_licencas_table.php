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
        Schema::create('licenca', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('licenca_tipo_id')->constrained('licenca_tipo', 'id')->cascadeOnDelete();
            $table->string('numero_licenca')->nullable();
            $table->string('modal')->nullable();
            $table->date('data_emissao')->nullable();
            $table->date('vencimento')->nullable();
            $table->string('numero_sei')->nullable();
            $table->string('processo_dnit')->nullable();
            $table->string('inicio_subtrecho')->nullable();
            $table->string('fim_subtrecho')->nullable();
            $table->string('soma_extensao')->nullable();
            $table->string('emissor')->nullable();
            $table->string('empreendimento')->nullable();
            $table->string('dias_renovacao')->nullable();
            $table->string('renovacao')->nullable();
            $table->string('requerimento')->nullable();
            $table->string('fiscal')->nullable();
            $table->text('observacoes')->nullable();
            $table->string('data_publicacao')->nullable();
            $table->string('in_app')->nullable();
            $table->string('out_app')->nullable();
            $table->string('volume')->nullable();
            $table->string('sinaflor')->nullable();
            $table->string('arquivo_licenca')->nullable();
            $table->string('file_shape')->nullable();
            $table->string('nome_file_shape')->nullable();
            $table->string('total_app')->nullable();
            $table->string('status')->nullable();
            $table->string('local_shape')->nullable();
            $table->string('arquivo_requerimento')->nullable();
            $table->longText('geo_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licencas');
    }
};
