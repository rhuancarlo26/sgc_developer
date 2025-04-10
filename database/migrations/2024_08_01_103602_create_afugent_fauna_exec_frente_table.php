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
        Schema::create('afugent_fauna_exec_frente', function (Blueprint $table) {
            $table->id();
            $table->integer('id_servico')->nullable();
            $table->string('rodovia', 255);
            $table->integer('uf_inicial')->nullable();
            $table->integer('uf_final')->nullable();
            $table->text('km_inicial')->nullable();
            $table->text('km_final')->nullable();
            $table->text('latitude_inicial')->nullable();
            $table->text('latitude_final')->nullable();
            $table->text('longitude_inicial')->nullable();
            $table->text('longitude_final')->nullable();
            $table->text('zona_inicial')->nullable();
            $table->text('zona_final')->nullable();
            $table->datetime('data_inicial')->nullable();
            $table->datetime('data_final')->nullable();
            $table->text('obs')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afugent_fauna_exec_frente');
    }
};
