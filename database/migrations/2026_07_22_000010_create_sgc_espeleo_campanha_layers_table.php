<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('sgc_espeleo_campanha_layers')) {
            return;
        }

        Schema::create('sgc_espeleo_campanha_layers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campanha_id');
            $table->unsignedBigInteger('map_layer_id');
            $table->string('tipo', 100)->nullable();
            $table->timestamps();

            $table->unique(['campanha_id', 'map_layer_id'], 'sgc_espeleo_campanha_layers_campanha_id_map_layer_id_unique');
            $table->index('tipo', 'sgc_espeleo_campanha_layers_tipo_index');
            $table->index('map_layer_id', 'sgc_espeleo_campanha_layers_map_layer_id_foreign');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgc_espeleo_campanha_layers');
    }
};
