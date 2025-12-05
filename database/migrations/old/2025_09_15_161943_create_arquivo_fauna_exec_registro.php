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
        Schema::create('arquivo_fauna_exec_registro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arquivo_id')->constrained('arquivos');
            $table->foreignId('fauna_exec_id')->constrained('fauna_exec_registros');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arquivo_fauna_exec_registro');
    }
};
