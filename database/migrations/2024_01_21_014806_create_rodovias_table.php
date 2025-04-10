<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rodovias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uf_id')->constrained('ufs');
            $table->string('rodovia');
            $table->timestamps();
        });

        $rodovias = require_once(database_path('seeders/array_base_rodovias.php'));
        foreach ($rodovias as $rodovia) {
            DB::table('rodovias')->insert($rodovia);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rodovias');
    }
};