<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('sgc_espeleo_campanha_justificativas')) {
            return;
        }

        Schema::create('sgc_espeleo_campanha_justificativas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campanha_id');
            $table->integer('id_contrato')->nullable();
            $table->text('codigo_sei')->nullable();
            $table->string('titulo')->nullable();
            $table->text('justificativa');
            $table->enum('tipo', ['citacao', 'complementar'])->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgc_espeleo_campanha_justificativas');
    }
};
