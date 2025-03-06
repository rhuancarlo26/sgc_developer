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
        Schema::create('licenca_tipo', function (Blueprint $table) {
            $table->id();
            $table->string('sigla');
            $table->string('nome');
            $table->timestamps();
        });

        $created_at = date('Y-m-d H:i:s');
        
        DB::table('licenca_tipo')->insert([
            ['sigla' => 'AA',   'nome' => 'Autorização Ambiental',                                              'created_at' => $created_at],
            ['sigla' => 'AFF',  'nome' => 'Autorização Ambiental de Funcionamento',                             'created_at' => $created_at],
            ['sigla' => 'ASV',  'nome' => 'Autorização de Supressão de Vegetação',                              'created_at' => $created_at],
            ['sigla' => 'LAR',  'nome' => 'Licença Ambiental de Regularização',                                 'created_at' => $created_at],
            ['sigla' => 'LAU',  'nome' => 'Licença Ambiental Única',                                            'created_at' => $created_at],
            ['sigla' => 'LI',   'nome' => 'Licença de Instalação',                                              'created_at' => $created_at],
            ['sigla' => 'LIO',  'nome' => 'Licença de Instalação e Operação',                                   'created_at' => $created_at],
            ['sigla' => 'LP',   'nome' => 'Licença Prévia',                                                     'created_at' => $created_at],
            ['sigla' => 'LIP',  'nome' => 'Licença Prévia e de Instalação',                                     'created_at' => $created_at],
            ['sigla' => 'LO',   'nome' => 'Licença de Operação',                                                'created_at' => $created_at],
            ['sigla' => 'AAP',  'nome' => 'Autorização Ambiental Preliminar',                                   'created_at' => $created_at],
            ['sigla' => 'ABio', 'nome' => 'Autorização de Captura, Coleta e Transporte de Material Biológico',  'created_at' => $created_at],
            ['sigla' => 'ACCT', 'nome' => 'Alterada para ABio',                                                 'created_at' => $created_at],
            ['sigla' => 'DEC',  'nome' => 'DEC',                                                                'created_at' => $created_at],
            ['sigla' => 'LA',   'nome' => 'Licença Ambiental',                                                  'created_at' => $created_at],
            ['sigla' => 'LL',   'nome' => 'Licença LL',                                                         'created_at' => $created_at],
            ['sigla' => 'LS',   'nome' => 'Licença Simplificada',                                               'created_at' => $created_at],
            ['sigla' => 'DL',   'nome' => 'Dispensa de Licenciamento',                                          'created_at' => $created_at],
            ['sigla' => 'AAP',  'nome' => 'Autorização Ambiental Preliminar',                                   'created_at' => $created_at]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenca_tipo');
    }
};
