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
        Schema::create('contrato_tipos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->timestamps();
        });

        $created_at = date('Y-m-d H:i:s');

        DB::table('contrato_tipos')->insert([
            ['nome' => 'Gestão Ambiental', 'created_at' => $created_at],
            ['nome' => 'Estudo Ambiental', 'created_at' => $created_at],
            ['nome' => 'Regularização Ambiental', 'created_at' => $created_at],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrato_tipos');
    }
};