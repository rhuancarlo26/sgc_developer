<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sgc_malarigeno_anexos', function (Blueprint $table) {
            $table->dropForeign('sgc_malarigeno_anexos_sgc_malarigeno_id_foreign');
        });

        Schema::rename('sgc_malarigeno_anexos', 'sgc_modulo_campanha_anexos');

        Schema::table('sgc_modulo_campanha_anexos', function (Blueprint $table) {
            $table->renameColumn('sgc_malarigeno_id', 'campanha_id');
        });

        Schema::table('sgc_modulo_campanha_anexos', function (Blueprint $table) {
            $table->foreign('campanha_id')
                ->references('id')
                ->on('sgc_modulo_campanhas')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sgc_modulo_campanha_anexos', function (Blueprint $table) {
            $table->dropForeign(['campanha_id']);
        });

        Schema::table('sgc_modulo_campanha_anexos', function (Blueprint $table) {
            $table->renameColumn('campanha_id', 'sgc_malarigeno_id');
        });

        Schema::rename('sgc_modulo_campanha_anexos', 'sgc_malarigeno_anexos');

        Schema::table('sgc_malarigeno_anexos', function (Blueprint $table) {
            $table->foreign('sgc_malarigeno_id')
                ->references('id')
                ->on('sgc_malarigeno')
                ->cascadeOnDelete();
        });
    }
};
