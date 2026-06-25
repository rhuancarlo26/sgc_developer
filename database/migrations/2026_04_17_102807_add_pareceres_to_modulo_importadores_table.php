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
        Schema::table('modulo_importadores', function (Blueprint $table) {

            $table->text('parecer_analise')->nullable()->after('load');
            $table->text('parecer_tecnico')->nullable()->after('load');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modulo_importadores', function (Blueprint $table) {
            $table->dropColumn('parecer_tecnico');
            $table->dropColumn('parecer_analise');
        });
    }
};
