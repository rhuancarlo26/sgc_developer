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
        Schema::create('afugent_fauna_config_abio', function (Blueprint $table) {
            $table->id();
            $table->string('chave', 255);
            $table->foreignId('id_servico')->constrained('servicos')->cascadeOnDelete();
            $table->foreignId('id_licenca')->constrained('licenca')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afugent_fauna_config_abio');
    }
};
