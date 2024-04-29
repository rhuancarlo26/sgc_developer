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
        Schema::create('contrato_introducao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->unique()->constrained('contratos', 'id')->cascadeOnDelete();
            $table->text('nome');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrato_introducao');
    }
};
