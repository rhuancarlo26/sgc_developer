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
        Schema::create('destinacao_pilhas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destinacao_id')->constrained('destinacaos');
            $table->foreignId('controle_pilha_id')->constrained('controle_pilhas');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinacao_pilhas');
    }
};
