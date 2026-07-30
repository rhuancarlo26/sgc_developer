<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('map_layers', 'thematic_field')) {
            return;
        }

        Schema::table('map_layers', function (Blueprint $table) {
            $table->string('thematic_field')->nullable()->after('crs');
            $table->json('thematic_style')->nullable()->after('thematic_field');
        });
    }

    public function down(): void
    {
        Schema::table('map_layers', function (Blueprint $table) {
            $table->dropColumn(['thematic_field', 'thematic_style']);
        });
    }
};
