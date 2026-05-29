<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgc_fauna_campanha', function (Blueprint $table) {
            $table->timestamp('arquivada_em')->nullable()->after('versao_analise');
            $table->string('etapa_atual')->nullable()->after('arquivada_em');
        });
    }

    public function down(): void
    {
        Schema::table('sgc_fauna_campanha', function (Blueprint $table) {
            $table->dropColumn(['arquivada_em', 'etapa_atual']);
        });
    }
};
