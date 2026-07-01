<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::rename('sgc_malarigeno', 'sgc_modulo_campanhas');

        Schema::table('sgc_modulo_campanhas', function (Blueprint $table) {
            $table->string('produto')->nullable()->after('id_contrato');
        });

        DB::table('sgc_modulo_campanhas')->update(['produto' => 'malarigeno']);
    }

    public function down(): void
    {
        Schema::table('sgc_modulo_campanhas', function (Blueprint $table) {
            $table->dropColumn('produto');
        });

        Schema::rename('sgc_modulo_campanhas', 'sgc_malarigeno');
    }
};
