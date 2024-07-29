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
        Schema::create('servico_pmqa_configuracao_parecer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servico_id')->unique()->constrained('servicos')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('servico_status')->cascadeOnDelete();
            $table->text('parecer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_pmqa_configuracao_parecer');
    }
};
