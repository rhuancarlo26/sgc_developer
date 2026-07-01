<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sgc_modulos', function (Blueprint $table) {
            $table->string('produto_slug')->nullable()->after('nome');
            $table->string('produto_titulo')->nullable()->after('produto_slug');
            $table->index('produto_slug');
        });

        DB::table('sgc_modulos')->update([
            'produto_slug' => 'malarigeno',
            'produto_titulo' => 'Malarígeno',
        ]);
    }

    public function down(): void
    {
        Schema::table('sgc_modulos', function (Blueprint $table) {
            $table->dropIndex(['produto_slug']);
            $table->dropColumn(['produto_slug', 'produto_titulo']);
        });
    }
};
