<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('servicos')) {
            return;
        }

        if (!Schema::hasColumn('servicos', 'servico_mod_imp_id')) {
            Schema::table('servicos', function (Blueprint $table) {
                $table->foreignId('servico_mod_imp_id')->nullable()->after('tema_servico')->constrained('modulos')->nullOnDelete();
                $table->unique(
                    ['id_contrato', 'tema_servico', 'servico_mod_imp_id'],
                    'servicos_contrato_tema_mod_imp_unique'
                );
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('servicos')) {
            return;
        }

        if (Schema::hasColumn('servicos', 'servico_mod_imp_id')) {
            Schema::table('servicos', function (Blueprint $table) {
                $table->dropUnique('servicos_contrato_tema_mod_imp_unique');
                $table->dropForeign(['servico_mod_imp_id']);
                $table->dropColumn('servico_mod_imp_id');
            });
        }
    }
};
