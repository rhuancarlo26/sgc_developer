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
        Schema::table('sgc_fauna_campanha', function (Blueprint $table) {
            $table->string('planilha_atropelamento')->nullable()->after('sei_dnit');
            $table->text('consideracoes_atropelamento')->nullable()->after('planilha_atropelamento');
            $table->string('num_abio')->nullable()->after('consideracoes_atropelamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sgc_fauna_campanha', function (Blueprint $table) {
            $table->dropColumn(['planilha_atropelamento', 'consideracoes_atropelamento', 'num_abio']);
        });
    }
};
