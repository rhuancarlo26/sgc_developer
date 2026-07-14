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
        Schema::table('area_supressao', function (Blueprint $table) {
            $table->decimal('area_em_app', 10, 4)->nullable()->change();
            $table->decimal('area_fora_app', 10, 4)->nullable()->change();
            $table->decimal('area_total', 10, 4)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('area_supressao', function (Blueprint $table) {
            $table->decimal('area_em_app', 8, 2)->nullable()->change();
            $table->decimal('area_fora_app', 8, 2)->nullable()->change();
            $table->decimal('area_total', 8, 2)->nullable()->change();
        });
    }
};
