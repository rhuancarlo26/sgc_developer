<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('modulos')) {
            return;
        }

        Schema::table('modulos', function (Blueprint $table) {
            if (!Schema::hasColumn('modulos', 'pmqa')) {
                $table->boolean('pmqa')->default(false)->after('nome');
            }

            if (!Schema::hasColumn('modulos', 'contrato_id')) {
                $table->unsignedBigInteger('contrato_id')->nullable()->after('pmqa');
                $table->foreign('contrato_id')->references('id')->on('contratos')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('modulos')) {
            return;
        }

        Schema::table('modulos', function (Blueprint $table) {
            if (Schema::hasColumn('modulos', 'contrato_id')) {
                $table->dropForeign(['contrato_id']);
                $table->dropColumn('contrato_id');
            }

            if (Schema::hasColumn('modulos', 'pmqa')) {
                $table->dropColumn('pmqa');
            }
        });
    }
};
