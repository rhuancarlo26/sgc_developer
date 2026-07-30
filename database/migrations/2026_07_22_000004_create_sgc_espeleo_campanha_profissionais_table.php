<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('sgc_espeleo_campanha_profissionais')) {
            return;
        }

        Schema::create('sgc_espeleo_campanha_profissionais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campanha_id');
            $table->unsignedBigInteger('id_modulo')->nullable();
            $table->unsignedBigInteger('id_contrato');
            $table->integer('profissional_id');
            $table->timestamps();

            $table->index('campanha_id', 'sgc_espeleo_campanha_profissionais_campanha_id_foreign');
            $table->index('profissional_id', 'sgc_espeleo_cp_profissional_id_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgc_espeleo_campanha_profissionais');
    }
};
