<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('rh', 'status')) {
            Schema::table('rh', function (Blueprint $table) {
                $table->boolean('status')->default(1)->after('obs');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('rh', 'status')) {
            Schema::table('rh', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
