<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sgc_espeleo_campanha_profissionais')) {
            return;
        }

        Schema::create('sgc_espeleo_campanha_profissionais', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('campanha_id')->index();
            $table->unsignedInteger('id_modulo')->nullable();
            $table->unsignedInteger('id_contrato')->index();
            $table->unsignedInteger('profissional_id')->index();
            $table->timestamps();

            $table->foreign('campanha_id')
                ->references('id')
                ->on('sgc_espeleo_campanhas')
                ->onDelete('cascade');

            $table->foreign('profissional_id')
                ->references('id')
                ->on('sgc_espeleo_profissionais')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgc_espeleo_campanha_profissionais');
    }
};
