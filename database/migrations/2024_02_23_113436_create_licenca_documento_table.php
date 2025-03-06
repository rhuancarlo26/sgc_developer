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
        Schema::create('licenca_documento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licenca_id')->unique()->constrained('licenca', 'id')->cascadeOnDelete();
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
        Schema::dropIfExists('licenca_documento');
    }
};