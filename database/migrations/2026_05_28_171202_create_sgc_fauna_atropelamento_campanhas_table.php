<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sgc_fauna_atropelamento_campanhas', function (Blueprint $table) {
            $table->id();
            $table->integer('campanha_id');
            $table->integer('id_contrato');
            $table->string('rodovia')->nullable();
            $table->date('data_inicial')->nullable();
            $table->date('data_final')->nullable();
            $table->char('uf_inicial', 2)->nullable();
            $table->char('uf_final', 2)->nullable();
            $table->decimal('km_inicial', 10, 3)->nullable();
            $table->decimal('km_final', 10, 3)->nullable();
            $table->decimal('latitude_inicial', 10, 7)->nullable();
            $table->decimal('longitude_inicial', 10, 7)->nullable();
            $table->decimal('latitude_final', 10, 7)->nullable();
            $table->decimal('longitude_final', 10, 7)->nullable();
            $table->text('obs')->nullable();
            $table->timestamps();

            $table->index('campanha_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgc_fauna_atropelamento_campanhas');
    }
};
