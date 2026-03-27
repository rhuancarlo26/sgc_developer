<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sgc_espeleo_resultados_anexos')) {
            return;
        }

        Schema::table('sgc_espeleo_resultados_anexos', function (Blueprint $table) {
            if (!Schema::hasColumn('sgc_espeleo_resultados_anexos', 'nome_arquivo')) {
                $table->string('nome_arquivo')->nullable()->after('campanha_id');
            }

            if (!Schema::hasColumn('sgc_espeleo_resultados_anexos', 'tipo')) {
                $table->string('tipo', 100)->nullable()->after('caminho');
            }

            if (!Schema::hasColumn('sgc_espeleo_resultados_anexos', 'comentario')) {
                $table->text('comentario')->nullable()->after('tipo');
            }
        });

        // Compatibilidade com schema legado: evita falha por colunas NOT NULL não usadas pelo novo fluxo.
        if (Schema::hasColumn('sgc_espeleo_resultados_anexos', 'nome')) {
            DB::statement('ALTER TABLE sgc_espeleo_resultados_anexos MODIFY nome VARCHAR(255) NULL');
            DB::statement('UPDATE sgc_espeleo_resultados_anexos SET nome_arquivo = nome WHERE nome_arquivo IS NULL AND nome IS NOT NULL');
        }

        if (Schema::hasColumn('sgc_espeleo_resultados_anexos', 'tipo_anexo')) {
            DB::statement("ALTER TABLE sgc_espeleo_resultados_anexos MODIFY tipo_anexo ENUM('anuencia_proprietarios','registro_fotografico','dados_secundarios','art','ret','cr','ctf','anuencia_colecoes','oficio_atividades_campo') NULL");
            DB::statement('UPDATE sgc_espeleo_resultados_anexos SET tipo = tipo_anexo WHERE tipo IS NULL AND tipo_anexo IS NOT NULL');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('sgc_espeleo_resultados_anexos')) {
            return;
        }

        Schema::table('sgc_espeleo_resultados_anexos', function (Blueprint $table) {
            if (Schema::hasColumn('sgc_espeleo_resultados_anexos', 'comentario')) {
                $table->dropColumn('comentario');
            }

            if (Schema::hasColumn('sgc_espeleo_resultados_anexos', 'tipo')) {
                $table->dropColumn('tipo');
            }

            if (Schema::hasColumn('sgc_espeleo_resultados_anexos', 'nome_arquivo')) {
                $table->dropColumn('nome_arquivo');
            }
        });
    }
};
