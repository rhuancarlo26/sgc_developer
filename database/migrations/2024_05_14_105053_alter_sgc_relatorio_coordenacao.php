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
        Schema::table('sgc_relatorio_coordenacao', function (Blueprint $table) {
            $table->foreign('id_item')
                ->references('item_id')
                ->on('relatorio_upload');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sgc_relatorio_coordenacao', function (Blueprint $table) {
            $table->dropForeign('sgc_relatorio_coordenacao_item_id_foreign');
        });
    }
};
