<?php

namespace Tests\Unit;

use App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\StoreEntregaSimplificadaFaunaRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class FaunaPlanilhasAtropelamentoTest extends TestCase
{
    public function test_roadkill_accepts_only_terrestrial_spreadsheets(): void
    {
        $request = new StoreEntregaSimplificadaFaunaRequest();
        $request->merge(['subproduto' => 'Execução da Campanha de Levantamento do ATROPELAMENTO de Fauna - Cerrado']);
        $rules = ['planilhas' => $request->rules()['planilhas']];
        $this->assertTrue(Validator::make(['planilhas' => ['terrestre' => ['modulo_id' => null]]], $rules)->passes());
        foreach (['aquatica', 'cavernicola'] as $tipo) {
            $this->assertTrue(Validator::make(['planilhas' => [$tipo => ['modulo_id' => null]]], $rules)->fails());
        }
        $this->assertTrue(Validator::make([], $rules)->passes());
    }

    public function test_other_fauna_subproducts_keep_all_modalities(): void
    {
        $request = new StoreEntregaSimplificadaFaunaRequest();
        $request->merge(['subproduto' => 'Execução do Levantamento de Fauna em Módulo Amostral - Cerrado']);
        $rules = ['planilhas' => $request->rules()['planilhas']];
        $this->assertTrue(Validator::make(['planilhas' => ['terrestre' => [], 'aquatica' => [], 'cavernicola' => []]], $rules)->passes());
    }
}
