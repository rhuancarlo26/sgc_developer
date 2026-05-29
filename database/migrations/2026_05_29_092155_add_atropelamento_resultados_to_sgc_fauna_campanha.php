<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgc_fauna_campanha', function (Blueprint $table) {
            $table->string('planilha_atropelamento')->nullable()->after('subproduto');
            $table->text('consideracoes_atropelamento')->nullable()->after('planilha_atropelamento');
        });
    }

    public function down(): void
    {
        Schema::table('sgc_fauna_campanha', function (Blueprint $table) {
            $table->dropColumn(['planilha_atropelamento', 'consideracoes_atropelamento']);
        });
    }
};
