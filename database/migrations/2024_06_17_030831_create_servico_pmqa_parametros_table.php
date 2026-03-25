<?php

use App\Models\ServicoPmqaParametro;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('servico_pmqa_parametros', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('unidade');
            $table->string('sigla');
            $table->string('classe_1');
            $table->string('classe_2');
            $table->string('classe_3');
            $table->string('classe_4');
            $table->integer('limite');
            $table->boolean('condicao_especial');
            $table->timestamps();
        });

        $data = [
            [
                'nome' => 'Condutividade Elétrica',
                'unidade' => 'μS/cm',
                'sigla' => 'CE',
                'classe_1' => 0,
                'classe_2' => 0,
                'classe_3' => 0,
                'classe_4' => 0,
                'limite' => 0,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Óleos e Graxas Totais',
                'unidade' => 'mg/L',
                'sigla' => 'O&G',
                'classe_1' => 0,
                'classe_2' => 0,
                'classe_3' => 0,
                'classe_4' => 0,
                'limite' => 0,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Temperatura',
                'unidade' => '°C',
                'sigla' => 'TEMP',
                'classe_1' => 0,
                'classe_2' => 0,
                'classe_3' => 0,
                'classe_4' => 0,
                'limite' => 0,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Coliformes Totais',
                'unidade' => 'NMP/100mL',
                'sigla' => '',
                'classe_1' => 0,
                'classe_2' => 0,
                'classe_3' => 0,
                'classe_4' => 0,
                'limite' => 0,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Nitrogênio Total',
                'unidade' => 'mg/L',
                'sigla' => '',
                'classe_1' => 0,
                'classe_2' => 0,
                'classe_3' => 0,
                'classe_4' => 0,
                'limite' => 0,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Nitrogênio Kjeldahl',
                'unidade' => 'mg/L',
                'sigla' => '',
                'classe_1' => 0,
                'classe_2' => 0,
                'classe_3' => 0,
                'classe_4' => 0,
                'limite' => 0,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Oxigênio Dissolvido',
                'unidade' => 'mg/L O2',
                'sigla' => 'OD',
                'classe_1' => '≥ 6,0',
                'classe_2' => '≥ 5',
                'classe_3' => '≥ 4',
                'classe_4' => '=>2',
                'limite' => 1,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Sólidos Dissolvidos Totais',
                'unidade' => 'mg/L',
                'sigla' => 'SDT',
                'classe_1' => '≤ 500',
                'classe_2' => '≤ 500',
                'classe_3' => '≤ 500',
                'classe_4' => null,
                'limite' => 1,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Turbidez',
                'unidade' => 'UNT',
                'sigla' => 'TURB',
                'classe_1' => '≤ 40',
                'classe_2' => '≤ 100',
                'classe_3' => '≤ 100',
                'classe_4' => null,
                'limite' => 1,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Demanda Bioquímica de Oxigênio',
                'unidade' => 'mg/L O2',
                'sigla' => 'DBO',
                'classe_1' => '≤ 3',
                'classe_2' => '≤ 5',
                'classe_3' => '≤ 10',
                'classe_4' => null,
                'limite' => 1,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Clorofila a',
                'unidade' => 'μg/L',
                'sigla' => '',
                'classe_1' => '≤ 10',
                'classe_2' => '≤ 30',
                'classe_3' => '≤ 60',
                'classe_4' => null,
                'limite' => 1,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Coliformes Termotolerantes',
                'unidade' => 'NMP/100mL',
                'sigla' => '',
                'classe_1' => '≤ 200',
                'classe_2' => '≤ 1000',
                'classe_3' => '≤ 1000',
                'classe_4' => null,
                'limite' => 1,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Ferro Dissolvido',
                'unidade' => 'mg/L',
                'sigla' => 'FE',
                'classe_1' => '≤ 0,3',
                'classe_2' => '≤ 0,3',
                'classe_3' => '≤ 5',
                'classe_4' => null,
                'limite' => 1,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Manganês Total',
                'unidade' => 'mg/L',
                'sigla' => 'Mn',
                'classe_1' => '≤ 0,1',
                'classe_2' => '≤ 0,1',
                'classe_3' => '≤ 0,5',
                'classe_4' => null,
                'limite' => 1,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Mercúrio Total',
                'unidade' => 'mg/L',
                'sigla' => 'Hg',
                'classe_1' => '≤ 0,0002',
                'classe_2' => '≤ 0,0002',
                'classe_3' => 0,
                'classe_4' => null,
                'limite' => 1,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Nitrato',
                'unidade' => 'mg/L',
                'sigla' => '',
                'classe_1' => '≤ 10',
                'classe_2' => '≤ 10',
                'classe_3' => '≤ 10',
                'classe_4' => null,
                'limite' => 1,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Nitrito',
                'unidade' => 'mg/L',
                'sigla' => '',
                'classe_1' => '≤ 1',
                'classe_2' => '≤ 1',
                'classe_3' => '≤ 1',
                'classe_4' => null,
                'limite' => 1,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Zinco Total',
                'unidade' => 'mg/L',
                'sigla' => 'Zn',
                'classe_1' => '≤ 0,18',
                'classe_2' => '≤ 0,18',
                'classe_3' => '≤ 5',
                'classe_4' => null,
                'limite' => 1,
                'condicao_especial' => 0
            ],
            [
                'nome' => 'Nitrogênio Amoniacal Total',
                'unidade' => 'mg/L',
                'sigla' => 'NAT',
                'classe_1' => '≤ 3,7',
                'classe_2' => '≤ 3,7',
                'classe_3' => '≤ 13,3',
                'classe_4' => null,
                'limite' => 1,
                'condicao_especial' => 1
            ],
            [
                'nome' => 'Fósforo Total',
                'unidade' => 'mg/L',
                'sigla' => 'P',
                'classe_1' => '≥ 0,020',
                'classe_2' => '≥ 0,1',
                'classe_3' => null,
                'classe_4' => null,
                'limite' => 1,
                'condicao_especial' => 1
            ],
            [
                'nome' => 'PH',
                'unidade' => '',
                'sigla' => '',
                'classe_1' => 0,
                'classe_2' => 0,
                'classe_3' => 0,
                'classe_4' => 0,
                'limite' => 1,
                'condicao_especial' => 1
            ]
        ];

        ServicoPmqaParametro::insert($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_pmqa_parametros');
    }
};
