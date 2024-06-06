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
        Schema::create('licenca_condicionantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licenca_id')->constrained('licenca', 'id')->cascadeOnDelete();
            $table->integer('numero_condicionante');
            $table->integer('prazo');
            $table->text('descricao');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenca_condicionante');
    }
};