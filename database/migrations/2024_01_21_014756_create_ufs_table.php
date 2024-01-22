<?php

use Carbon\Carbon;
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
        Schema::create('ufs', function (Blueprint $table) {
            $table->id();
            $table->string('estado');
            $table->string('uf');
            $table->timestamps();
        });

        DB::table('ufs')->insert([
            ['estado' => 'Acre', 'uf' => 'AC', 'created_at' => Carbon::now()],
            ['estado' => 'Alagoas', 'uf' => 'AL', 'created_at' => Carbon::now()],
            ['estado' => 'Amazonas', 'uf' => 'AM', 'created_at' => Carbon::now()],
            ['estado' => 'Amapá', 'uf' => 'AP', 'created_at' => Carbon::now()],
            ['estado' => 'Bahia', 'uf' => 'BA', 'created_at' => Carbon::now()],
            ['estado' => 'Ceará', 'uf' => 'CE', 'created_at' => Carbon::now()],
            ['estado' => 'Distrito Federal', 'uf' => 'DF', 'created_at' => Carbon::now()],
            ['estado' => 'Espírito Santo', 'uf' => 'ES', 'created_at' => Carbon::now()],
            ['estado' => 'Goiás', 'uf' => 'GO', 'created_at' => Carbon::now()],
            ['estado' => 'Maranhão', 'uf' => 'MA', 'created_at' => Carbon::now()],
            ['estado' => 'Minas Gerais', 'uf' => 'MG', 'created_at' => Carbon::now()],
            ['estado' => 'Mato Grosso do Sul', 'uf' => 'MS', 'created_at' => Carbon::now()],
            ['estado' => 'Mato Grosso', 'uf' => 'MT', 'created_at' => Carbon::now()],
            ['estado' => 'Pará', 'uf' => 'PA', 'created_at' => Carbon::now()],
            ['estado' => 'Paraíba', 'uf' => 'PB', 'created_at' => Carbon::now()],
            ['estado' => 'Pernambuco', 'uf' => 'PE', 'created_at' => Carbon::now()],
            ['estado' => 'Piauí', 'uf' => 'PI', 'created_at' => Carbon::now()],
            ['estado' => 'Paraná', 'uf' => 'PR', 'created_at' => Carbon::now()],
            ['estado' => 'Rio de Janeiro', 'uf' => 'RJ', 'created_at' => Carbon::now()],
            ['estado' => 'Rio Grande do Norte', 'uf' => 'RN', 'created_at' => Carbon::now()],
            ['estado' => 'Rondônia', 'uf' => 'RO', 'created_at' => Carbon::now()],
            ['estado' => 'Roraima', 'uf' => 'RR', 'created_at' => Carbon::now()],
            ['estado' => 'Rio Grande do Sul', 'uf' => 'RS', 'created_at' => Carbon::now()],
            ['estado' => 'Santa Catarina', 'uf' => 'SC', 'created_at' => Carbon::now()],
            ['estado' => 'Sergipe', 'uf' => 'SE', 'created_at' => Carbon::now()],
            ['estado' => 'São Paulo', 'uf' => 'SP', 'created_at' => Carbon::now()],
            ['estado' => 'Tocantins', 'uf' => 'TO', 'created_at' => Carbon::now()]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ufs');
    }
};