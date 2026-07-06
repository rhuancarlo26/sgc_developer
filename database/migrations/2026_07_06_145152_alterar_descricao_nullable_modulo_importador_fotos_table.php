<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('modulo_importador_fotos')) {
            return;
        }

        if (!Schema::hasColumn('modulo_importador_fotos', 'descricao')) {
            return;
        }

        DB::statement('ALTER TABLE `modulo_importador_fotos` MODIFY `descricao` TEXT NULL');
    }

    public function down(): void
    {
        if (!Schema::hasTable('modulo_importador_fotos')) {
            return;
        }

        if (!Schema::hasColumn('modulo_importador_fotos', 'descricao')) {
            return;
        }

        DB::table('modulo_importador_fotos')
            ->whereNull('descricao')
            ->update(['descricao' => '']);

        DB::statement('ALTER TABLE `modulo_importador_fotos` MODIFY `descricao` TEXT NOT NULL');
    }
};
