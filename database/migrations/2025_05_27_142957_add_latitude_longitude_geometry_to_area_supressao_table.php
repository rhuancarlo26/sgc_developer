<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::table('area_supressao', function (Blueprint $table) {
            
            $table->decimal('latitude', 9, 6)
                  ->after('observacao')
                  ->comment('Latitude em graus decimais');

            $table->decimal('longitude', 9, 6)
                  ->after('latitude')
                  ->comment('Longitude em graus decimais');

           
            $table->json('geometry')
                  ->after('longitude')
                  ->nullable()
                  ->comment('GeoJSON Point { "type":"Point", "coordinates":[lng,lat] }');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('area_supressao', function (Blueprint $table) {
            $table->dropColumn(['geometry', 'longitude', 'latitude']);
        });
    }
};
