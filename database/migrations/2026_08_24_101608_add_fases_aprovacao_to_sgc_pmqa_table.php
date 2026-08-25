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
        Schema::table('sgc_pmqa', function (Blueprint $table) {
            $table->string('status_apresentacao', 30)->default('Em elaboração')->after('status_aprovacao');
            $table->string('status_configuracao', 30)->default('Bloqueado')->after('status_apresentacao');
            $table->string('status_execucao', 30)->default('Bloqueado')->after('status_configuracao');
            $table->string('status_resultado', 30)->default('Bloqueado')->after('status_execucao');
            $table->string('status_relatorio', 30)->default('Bloqueado')->after('status_resultado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sgc_pmqa', function (Blueprint $table) {
            $table->dropColumn([
                'status_apresentacao',
                'status_configuracao',
                'status_execucao',
                'status_resultado',
                'status_relatorio'
            ]);
        });
    }
};
