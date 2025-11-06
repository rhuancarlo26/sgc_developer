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
        Schema::table('plano_supressao', function (Blueprint $table) {
            $table->longText('local_shape_em_app')->nullable()->change();
            $table->longText('local_shape_fora_app')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plano_supressao', function (Blueprint $table) {
            $table->string('local_shape_em_app', 100)->nullable()->change();
            $table->string('local_shape_fora_app', 100)->nullable()->change();
        });
    }
};
