<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modulo_importadores', function (Blueprint $table) {
            if (!Schema::hasColumn('modulo_importadores', 'servico_id')) {
                $table->unsignedInteger('servico_id')
                    ->nullable()
                    ->after('contrato_id');

                $table->foreign('servico_id')
                    ->references('id')
                    ->on('servicos')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('modulo_importadores', function (Blueprint $table) {
            if (Schema::hasColumn('modulo_importadores', 'servico_id')) {
                $table->dropForeign(['servico_id']);
                $table->dropColumn('servico_id');
            }
        });
    }
};
