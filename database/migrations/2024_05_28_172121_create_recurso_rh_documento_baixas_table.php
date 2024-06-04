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
        Schema::create('recurso_rh_documento_baixas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recurso_rh_id')->constrained('recurso_rh', 'id')->cascadeOnDelete();
            $table->string('nome');
            $table->string('caminho');
            $table->string('tipo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurso_rh_documento_baixas');
    }
};
