<?php

use Illuminate\Support\Facades\Route;

use App\Domain\Sgc\Contratada\app\Controller\ContratoSgcController;
use App\Domain\Sgc\Contratada\app\Controller\DadosGeraisSgcController;
use App\Domain\Sgc\Contratada\app\Controller\RecursoSgcController;
use App\Domain\Sgc\Contratada\RelatorioCoord\Controller\StoreUploadRelatorioController;
use App\Domain\Sgc\Contratada\RelatorioCoord\Controller\VisualizarDocxController;;
use App\Domain\Sgc\Contratada\RelatorioCoord\Controller\DestroyUploadRelatorioController;
use App\Domain\Contrato\Contratada\DadosGerais\Introducao\Controller\StoreIntroducaoContratadaController;


Route::prefix('/contratada')->group(function () {
    Route::get('{contrato}/',                                      [ContratoSgcController::class,                           'index'])->name('sgc.contratada.index');
    Route::get('{contrato}/dados_gerais',                          [DadosGeraisSgcController::class,                        'index'])->name('sgc.contratada.dados_gerais.index');
    Route::get('{contrato}/recurso',                               [RecursoSgcController::class,                            'index'])->name('sgc.contratada.recurso.index');
    Route::post('/store_introducao',                               [StoreIntroducaoContratadaController::class,             'index'])->name('contratos.contratada.store_introducao.index');

    Route::post('/store_upload',                                   [StoreUploadRelatorioController::class,                  'index'])->name('sgc.contratada.store_anexo');
    Route::get('/sgc/relatorio-coordenacao', function () {
        $itens = App\Models\SgcRelatorioCoordenacao::all();
        return response()->json($itens);
    })->name('sgc.relatorio_coordenacao.index');


    Route::get('/sgc/visualizar',                                  [VisualizarDocxController::class,                         'index'])->name('sgc.contratada.visualizar_doc');

});
