<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('sgc_espeleo_estudos_posteriores')) {
            return;
        }

        Schema::create('sgc_espeleo_estudos_posteriores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subproduto_id');
            $table->integer('quantidade');
            $table->string('coordenadas');
            $table->boolean('necessario')->default(false);
            $table->integer('campanha_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgc_espeleo_estudos_posteriores');
    }
};
