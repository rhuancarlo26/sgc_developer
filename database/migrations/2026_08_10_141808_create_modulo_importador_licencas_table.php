<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('modulo_importador_licencas')) {
            return;
        }

        Schema::create('modulo_importador_licencas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('modulo_importador_id');
            $table->integer('licenca_id');
            $table->timestamps();
            $table->foreign('modulo_importador_id', 'mod_imp_lic_imp_fk')
                ->references('id')->on('modulo_importadores')->cascadeOnDelete();
            $table->foreign('licenca_id', 'mod_imp_lic_lic_fk')
                ->references('id')->on('licencas')->cascadeOnDelete();
            $table->unique(
                ['modulo_importador_id', 'licenca_id'],
                'mod_imp_lic_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modulo_importador_licencas');
    }
};
