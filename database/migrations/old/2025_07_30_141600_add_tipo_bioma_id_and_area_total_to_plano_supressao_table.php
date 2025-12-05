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
        Schema::table('plano_supressao', function (Blueprint $table) {
           
            $table->foreignId('tipo_bioma_id')
                  ->after('servico_id')
                  ->constrained('tipo_biomas')
                  ->onDelete('cascade');

            
            $table->decimal('area_total', 15, 2)
                  ->after('area_fora_app');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plano_supressao', function (Blueprint $table) {
            
            $table->dropForeign(['tipo_bioma_id']);
            $table->dropColumn(['tipo_bioma_id', 'area_total']);
        });
    }
};
