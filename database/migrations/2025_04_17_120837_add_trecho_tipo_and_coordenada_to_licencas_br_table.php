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
        Schema::table('licencas_br', function (Blueprint $table) {
            $table->char('trecho_tipo', 1)->after('extensao_br');
            $table->json('coordenada')->after('trecho_tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licencas_br', function (Blueprint $table) {
            $table->dropColumn(['trecho_tipo', 'coordenada']);
        });
    }
};
