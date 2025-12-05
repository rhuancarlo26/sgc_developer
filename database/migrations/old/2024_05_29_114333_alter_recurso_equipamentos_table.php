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
        Schema::table('recurso_equipamentos', function (Blueprint $table) {
            $table->boolean('alugado');
            $table->string('numero_serie');
            $table->date('ultima_calibracao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recurso_equipamentos', function (Blueprint $table) {
            $table->dropColumn('alugado');
            $table->dropColumn('numero_serie');
            $table->dropColumn('ultima_calibracao');
        });
    }
};
