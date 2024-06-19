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
        Schema::table('recurso_veiculos', function (Blueprint $table) {
            $table->boolean('alugado');
            $table->double('km_inicial');
            $table->string('placa_veiculo');
            $table->date('ultima_revisao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
