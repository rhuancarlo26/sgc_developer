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
        Schema::create('at_fauna_config_vinculacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_licenca')->constrained('licenca')->cascadeOnDelete();
            $table->foreignId('fk_servico')->constrained('servicos')->cascadeOnDelete();
            $table->text('shapefile');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('at_fauna_config_vinculacao');
    }
};

