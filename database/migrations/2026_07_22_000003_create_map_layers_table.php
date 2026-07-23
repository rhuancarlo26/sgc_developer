<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('map_layers')) {
            return;
        }

        Schema::create('map_layers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('workspace', 64);
            $table->string('datastore', 64);
            $table->string('layer_name', 64);
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('geometry_type', ['Point', 'LineString', 'Polygon', 'MultiPolygon']);
            $table->string('crs', 32)->default('EPSG:4674')->nullable();
            $table->longText('bbox')->nullable();
            $table->string('storage_path');
            $table->boolean('visible')->default(true);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('published_at')->nullable();

            $table->unique(['workspace', 'layer_name'], 'uniq_layer');
            $table->index('user_id', 'idx_user');
        });

        DB::statement('ALTER TABLE map_layers ADD CONSTRAINT map_layers_chk_1 CHECK (json_valid(`bbox`))');
    }

    public function down(): void
    {
        Schema::dropIfExists('map_layers');
    }
};
