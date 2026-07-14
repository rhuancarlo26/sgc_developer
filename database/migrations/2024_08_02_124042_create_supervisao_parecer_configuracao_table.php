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
        Schema::create('supervisao_parecer_configuracao', function (Blueprint $table) {
            $table->id();
            $table->string('fk_servico');
            $table->string('fk_status');
            $table->string('parecer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisao_parecer_configuracao');
    }
};
