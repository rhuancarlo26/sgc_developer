<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rh')) {
            return;
        }

        if (!Schema::hasColumn('rh', 'status')) {
            Schema::table('rh', function (Blueprint $table) {
                $table->boolean('status')->default(true)->after('numero_registro');
            });

            DB::table('rh')->whereNull('status')->update(['status' => true]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('rh')) {
            return;
        }

        if (Schema::hasColumn('rh', 'status')) {
            Schema::table('rh', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
