<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgc_patrimonio_shapefile', function (Blueprint $table) {
            $table->string('workspace')->nullable()->after('geo_json');
            $table->string('datastore')->nullable()->after('workspace');
            $table->string('layer_name')->nullable()->after('datastore');
            $table->string('storage_path')->nullable()->after('layer_name');
            $table->timestamp('published_at')->nullable()->after('storage_path');
        });
    }

    public function down(): void
    {
        Schema::table('sgc_patrimonio_shapefile', function (Blueprint $table) {
            $table->dropColumn([
                'workspace',
                'datastore',
                'layer_name',
                'storage_path',
                'published_at',
            ]);
        });
    }
};
