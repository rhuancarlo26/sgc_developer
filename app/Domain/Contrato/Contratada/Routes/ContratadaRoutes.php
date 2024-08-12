<?php

use Illuminate\Support\Facades\Route;

use App\Domain\Contrato\Contratada\app\Controller\ContratoContratadaController;
use App\Domain\Contrato\Contratada\app\Controller\DadosGeraisContratadaController;
use App\Domain\Contrato\Contratada\DadosGerais\Anexo\Controller\DestroyAnexoContratadaController;
use App\Domain\Contrato\Contratada\DadosGerais\Anexo\Controller\StoreAnexoContratadaController;
use App\Domain\Contrato\Contratada\DadosGerais\Anexo\Controller\UpdateAnexoContratadaController;
use App\Domain\Contrato\Contratada\DadosGerais\Anexo\Controller\VisualizarAnexoContratadaController;
use App\Domain\Contrato\Contratada\DadosGerais\Empreendimento\Controller\DestroyContratadaEmpreendimentoTrechoController;
use App\Domain\Contrato\Contratada\DadosGerais\Empreendimento\Controller\StoreContratadaEmpreendimentoTrechoController;
use App\Domain\Contrato\Contratada\DadosGerais\Empreendimento\Controller\UpdateContratadaEmpreendimentoTrechoController;
use App\Domain\Contrato\Contratada\DadosGerais\Historico\Controller\DestroyHistoricoContratadaController;
use App\Domain\Contrato\Contratada\DadosGerais\Historico\Controller\StoreHistoricoContratadaController;
use App\Domain\Contrato\Contratada\DadosGerais\Historico\Controller\UpdateHistoricoContratadaController;
use App\Domain\Contrato\Contratada\DadosGerais\Introducao\Controller\UpdateIntroducaoContratadaController;
use App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Controller\DeleteLicenciamentoContratadaController;
use App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Controller\StoreLicenciamentoContratadaController;
use App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Observacao\Controller\DeleteLicenciamentoObservacaoController;
use App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Observacao\Controller\StoreLicenciamentoObservacaoController;
use App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Observacao\Controller\UpdateLicenciamentoObservacaoController;
use App\Domain\Servico\app\Controller\CreateServicosContratadaController;
use App\Domain\Servico\app\Controller\DeleteServicoContratadaController;
use App\Domain\Servico\app\Controller\IndexServicosContratadaController;
use App\Domain\Servico\app\Controller\StoreServicosContratadaController;
use App\Domain\Servico\app\Controller\UpdateServicosContratadaController;
use App\Domain\Servico\Condicionante\Controller\DeleteLicencaCondicionanteServicoController;
use App\Domain\Servico\Condicionante\Controller\StoreLicencaCondicionanteServicoController;
use App\Domain\Servico\Equipamento\Controller\DeleteServicoEquipamentoContratadaController;
use App\Domain\Servico\Equipamento\Controller\StoreServicoEquipamentoContratadaController;
use App\Domain\Servico\Rh\Controller\DeleteServicoRhContratadaController;
use App\Domain\Servico\Rh\Controller\StoreServicoRhContratadaController;
use App\Domain\Servico\Veiculo\Controller\DeleteServicoVeiculoContratadaController;
use App\Domain\Servico\Veiculo\Controller\StoreServicoVeiculoContratadaController;
use App\Domain\Servico\app\Controller\EnviaServicoFiscalController;

Route::prefix('/contratada')->group(function () {
    Route::get('{contrato}/',                                      [ContratoContratadaController::class,                    'index'])->name('contratos.contratada.index');
    Route::get('{contrato}/dados_gerais',                          [DadosGeraisContratadaController::class,                 'index'])->name('contratos.contratada.dados_gerais.index');
    Route::patch('/update_introducao/{contrato}',                  [UpdateIntroducaoContratadaController::class,            'index'])->name('contratos.contratada.update_introducao.index');
    Route::post('/store_licenciamento',                            [StoreLicenciamentoContratadaController::class,          'index'])->name('contratos.contratada.store_licenciamento');
    Route::post('/delete_licenciamento/{licenca}',                 [DeleteLicenciamentoContratadaController::class,         'index'])->name('contratos.contratada.delete_licenciamento');
    Route::post('/store_licenciamento_observacao',                 [StoreLicenciamentoObservacaoController::class,          'index'])->name('contratos.contratada.store_licenciamento_observacao');
    Route::patch('/update_licenciamento_observacao/{observacao}',  [UpdateLicenciamentoObservacaoController::class,         'index'])->name('contratos.contratada.update_licenciamento_observacao');
    Route::delete('/delete_licenciamento_observacao/{observacao}', [DeleteLicenciamentoObservacaoController::class,         'index'])->name('contratos.contratada.delete_licenciamento_observacao');
    Route::post('/store_historico',                                [StoreHistoricoContratadaController::class,              'index'])->name('contratos.contratada.store_historico');
    Route::patch('/update_historico',                              [UpdateHistoricoContratadaController::class,             'index'])->name('contratos.contratada.update_historico');
    Route::delete('/destroy_historico/{historico}',                [DestroyHistoricoContratadaController::class,            'index'])->name('contratos.contratada.destroy_historico');
    Route::post('/store_anexo',                                    [StoreAnexoContratadaController::class,                  'index'])->name('contratos.contratada.store_anexo');
    Route::post('/update_anexo',                                   [UpdateAnexoContratadaController::class,                 'index'])->name('contratos.contratada.update_anexo');
    Route::delete('/destroy_anexo/{anexo}',                        [DestroyAnexoContratadaController::class,                'index'])->name('contratos.contratada.destroy_anexo');
    Route::get('/visualizar/{anexo}',                              [VisualizarAnexoContratadaController::class,             'index'])->name('contratos.contratada.visualizar_anexo');
    Route::post('/store_empreendimento_trecho',                    [StoreContratadaEmpreendimentoTrechoController::class,   'index'])->name('contratos.contratada.store_empreendimento_trecho');
    Route::patch('/update_empreendimento_trecho',                  [UpdateContratadaEmpreendimentoTrechoController::class,  'index'])->name('contratos.contratada.update_empreendimento_trecho');
    Route::delete('/destroy_empreendimento_trecho/{trecho}',       [DestroyContratadaEmpreendimentoTrechoController::class, 'index'])->name('contratos.contratada.destroy_empreendimento_trecho');


    //Equipamento
    require __DIR__ . '/../Recurso/Equipamento/Router/EquipamentoRouter.php';

    //Veiculo
    require __DIR__ . '/../Recurso/Veiculo/Router/VeiculoRouter.php';

    //RH
    require __DIR__ . '/../Recurso/Rh/Router/RhRouter.php';

    Route::prefix('/')->group(function () {
        Route::get('{contrato}/servicos',                   [IndexServicosContratadaController::class, 'index'])->name('contratos.contratada.servicos.index');
        Route::get('{contrato}/servicos/create/{servico?}', [CreateServicosContratadaController::class, 'index'])->name('contratos.contratada.servicos.create');
        Route::post('servicos/store',                       [StoreServicosContratadaController::class, 'index'])->name('contratos.contratada.servicos.store');
        Route::patch('servicos/update',                     [UpdateServicosContratadaController::class, 'index'])->name('contratos.contratada.servicos.update');
        Route::delete('servicos/delete/{servico}',          [DeleteServicoContratadaController::class, 'index'])->name('contratos.contratada.servicos.delete');
        Route::post('servicos/envia-fiscal/{servico}',      [EnviaServicoFiscalController::class, 'index'])->name('contratos.contratada.servicos.envia-fiscal');

        Route::prefix('servico/rh')->group(function () {
            Route::post('store',                            [StoreServicoRhContratadaController::class, 'index'])->name('contratos.contratada.servicos.rh.store');
            Route::post('delete/{rh}',                      [DeleteServicoRhContratadaController::class, 'index'])->name('contratos.contratada.servicos.rh.delete');
        });
        Route::prefix('servico/veiculo')->group(function () {
            Route::post('store',                            [StoreServicoVeiculoContratadaController::class, 'index'])->name('contratos.contratada.servicos.veiculo.store');
            Route::post('delete/{veiculo}',                 [DeleteServicoVeiculoContratadaController::class, 'index'])->name('contratos.contratada.servicos.veiculo.delete');
        });
        Route::prefix('servico/equipamento')->group(function () {
            Route::post('store',                            [StoreServicoEquipamentoContratadaController::class, 'index'])->name('contratos.contratada.servicos.equipamento.store');
            Route::post('delete/{equipamento}',             [DeleteServicoEquipamentoContratadaController::class, 'index'])->name('contratos.contratada.servicos.equipamento.delete');
        });
        Route::prefix('servico/condicionante')->group(function () {
            Route::post('store',                            [StoreLicencaCondicionanteServicoController::class, 'index'])->name('contratos.contratada.servicos.condicionante.store');
            Route::post('delete',                           [DeleteLicencaCondicionanteServicoController::class, 'index'])->name('contratos.contratada.servicos.condicionante.delete');
        });

        Route::prefix('servico')->group(function () {
            require __DIR__ . '/../../../Servico/PMQA/app/Routes/PMQARoutes.php';
            require __DIR__ . '/../../../Servico/AfugentamentoResgateFauna/app/Routes/AfugentamentoResgateFaunaRoutes.php';
            require __DIR__ . '/../../../Servico/SupressaoVegetacao/app/Routes/SupressaoVegetacaoRoutes.php';
        });
    });
});
