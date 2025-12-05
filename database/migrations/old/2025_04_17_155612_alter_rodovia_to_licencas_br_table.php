<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('licencas_br', function (Blueprint $table) {
            $table->unsignedBigInteger('rodovia_temp')->nullable()->after('rodovia');
        });

        DB::table('licencas_br')->orderBy('idlicenca_br')->chunk(100, function ($registros) {
            foreach ($registros as $registro) {
                if ($registro->rodovia) {
                    $rodovia = trim($registro->rodovia);
                    $baseRodovia = DB::table('base_rodovia')->where('rodovia', $rodovia)->where('estados_id', $registro->uf_inicial)->first();

                    if ($baseRodovia) {
                        DB::table('licencas_br')
                            ->where('idlicenca_br', $registro->idlicenca_br)
                            ->update(['rodovia_temp' => $baseRodovia->id]);
                    }
                }
            }
        });

        Schema::table('licencas_br', function (Blueprint $table) {
            $table->dropColumn('rodovia');
        });

        Schema::table('licencas_br', function (Blueprint $table) {
            $table->renameColumn('rodovia_temp', 'rodovia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licencas_br', function (Blueprint $table) {
            //
        });
    }
};
