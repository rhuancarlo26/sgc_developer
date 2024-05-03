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
        Schema::table('licenca', function (Blueprint $table) {
            $table->text('observacao')->nullable();
            $table->dropColumn('obs_renovacao');
            $table->dropColumn('arquivo_licenca');
            $table->dropColumn('file_shape');
            $table->dropColumn('nome_file_shape');
            $table->dropColumn('local_shape');
            $table->dropColumn('arquivo_requerimento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licenca', function (Blueprint $table) {
            $table->dropColumn('observacao');
            $table->text('obs_renovacao');
            $table->string('arquivo_licenca');
            $table->string('file_shape');
            $table->string('nome_file_shape');
            $table->string('local_shape');
            $table->string('arquivo_requerimento');
        });
    }
};
