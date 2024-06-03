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
use App\Domain\Contrato\Contratada\DadosGerais\Introducao\Controller\StoreIntroducaoContratadaController;
use App\Domain\Contrato\Contratada\DadosGerais\Introducao\Controller\UpdateIntroducaoContratadaController;
use App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Controller\DeleteLicenciamentoContratadaController;
use App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Controller\StoreLicenciamentoContratadaController;
use App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Observacao\Controller\DeleteLicenciamentoObservacaoController;
use App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Observacao\Controller\StoreLicenciamentoObservacaoController;
use App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Observacao\Controller\UpdateLicenciamentoObservacaoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\CreateEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\DestroyDocumentoEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\DestroyEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\ListagemEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\StoreDocumentoEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\StoreEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\VisualizarDocumentoEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\CreateRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\DestroyDocumentoRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\DestroyRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\ListagemRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\StoreDocumentoRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\StoreRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\UpdateRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Rh\Controller\VisualizarDocumentoRhRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\CreateVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\DestroyDocumentoVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\DestroyVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\ListagemVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\StoreDocumentoVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\StoreVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\UpdateVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\VisualizarDocumentoVeiculoRecursoController;
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

Route::prefix('/contratada')->group(function () {
    Route::get('{contrato}/',                                      [ContratoContratadaController::class,                    'index'])->name('contratos.contratada.index');
    Route::get('{contrato}/dados_gerais',                          [DadosGeraisContratadaController::class,                 'index'])->name('contratos.contratada.dados_gerais.index');
    Route::post('/store_introducao',                               [StoreIntroducaoContratadaController::class,             'index'])->name('contratos.contratada.store_introducao.index');
    Route::patch('/update_introducao/{introducao}',                [UpdateIntroducaoContratadaController::class,            'index'])->name('contratos.contratada.update_introducao.index');
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

    Route::get('{contrato}/recurso/equipamento',                           [ListagemEquipamentoRecursoController::class,            'index'])->name('contratos.contratada.recurso.equipamento.index');
    Route::get('{contrato}/recurso/equipamento/create/{equipamento?}',     [CreateEquipamentoRecursoController::class,              'index'])->name('contratos.contratada.recurso.equipamento.create');
    Route::post('recurso/equipamento/store',                               [StoreEquipamentoRecursoController::class,               'index'])->name('contratos.contratada.recurso.equipamento.store');
    Route::post('recurso/equipamento/store_documento',                     [StoreDocumentoEquipamentoRecursoController::class,      'index'])->name('contratos.contratada.recurso.equipamento.store_documento');
    Route::get('recurso/equipamento/visualizar/{documento}',               [VisualizarDocumentoEquipamentoRecursoController::class, 'index'])->name('contratos.contratada.recurso.equipamento.visualizar');
    Route::delete('{contrato}/recurso/equipamento/destroy/{documento}',    [DestroyDocumentoEquipamentoRecursoController::class,    'index'])->name('contratos.contratada.recurso.equipamento.destroy');
    Route::delete('recurso/equipamento/destroy_equipamento/{equipamento}', [DestroyEquipamentoRecursoController::class,             'index'])->name('contratos.contratada.recurso.equipamento.destroy_equipamento');

    Route::get('{contrato}/recurso/veiculo',                        [ListagemVeiculoRecursoController::class,            'index'])->name('contratos.contratada.recurso.veiculo.index');
    Route::get('{contrato}/recurso/veiculo/create/{veiculo?}',      [CreateVeiculoRecursoController::class,              'index'])->name('contratos.contratada.recurso.veiculo.create');
    Route::post('recurso/veiculo/store',                            [StoreVeiculoRecursoController::class,               'index'])->name('contratos.contratada.recurso.veiculo.store');
    Route::patch('recurso/veiculo/update',                          [UpdateVeiculoRecursoController::class,              'index'])->name('contratos.contratada.recurso.veiculo.update');
    Route::post('recurso/veiculo/store_documento',                  [StoreDocumentoVeiculoRecursoController::class,      'index'])->name('contratos.contratada.recurso.veiculo.store_documento');
    Route::get('recurso/veiculo/visualizar/{documento}',            [VisualizarDocumentoVeiculoRecursoController::class, 'index'])->name('contratos.contratada.recurso.veiculo.visualizar');
    Route::delete('{contrato}/recurso/veiculo/destroy/{documento}', [DestroyDocumentoVeiculoRecursoController::class,    'index'])->name('contratos.contratada.recurso.veiculo.destroy');
    Route::delete('recurso/veiculo/destroy_veiculo/{veiculo}',      [DestroyVeiculoRecursoController::class,             'index'])->name('contratos.contratada.recurso.veiculo.destroy_veiculo');

    Route::get('{contrato}/recurso/rh',                        [ListagemRhRecursoController::class,            'index'])->name('contratos.contratada.recurso.rh.index');
    Route::get('{contrato}/recurso/rh/create/{rh?}',           [CreateRhRecursoController::class,              'index'])->name('contratos.contratada.recurso.rh.create');
    Route::post('recurso/rh/store',                            [StoreRhRecursoController::class,               'index'])->name('contratos.contratada.recurso.rh.store');
    Route::patch('recurso/rh/update/{rh}',                     [UpdateRhRecursoController::class,              'index'])->name('contratos.contratada.recurso.rh.update');
    Route::post('recurso/rh/store_documento',                  [StoreDocumentoRhRecursoController::class,      'index'])->name('contratos.contratada.recurso.rh.store_documento');
    Route::get('recurso/rh/visualizar/{documento}',            [VisualizarDocumentoRhRecursoController::class, 'index'])->name('contratos.contratada.recurso.rh.visualizar');
    Route::delete('{contrato}/recurso/rh/destroy/{documento}', [DestroyDocumentoRhRecursoController::class,    'index'])->name('contratos.contratada.recurso.rh.destroy');
    Route::delete('recurso/rh/destroy_rh/{rh}',                [DestroyRhRecursoController::class,             'index'])->name('contratos.contratada.recurso.rh.destroy_rh');

    Route::prefix('/')->group(function () {
        Route::get('{contrato}/servicos',                   [IndexServicosContratadaController::class, 'index'])->name('contratos.contratada.servicos.index');
        Route::get('{contrato}/servicos/create/{servico?}', [CreateServicosContratadaController::class, 'index'])->name('contratos.contratada.servicos.create');
        Route::post('servicos/store',                       [StoreServicosContratadaController::class, 'index'])->name('contratos.contratada.servicos.store');
        Route::patch('servicos/update',                     [UpdateServicosContratadaController::class, 'index'])->name('contratos.contratada.servicos.update');
        Route::delete('servicos/delete/{servico}',          [DeleteServicoContratadaController::class, 'index'])->name('contratos.contratada.servicos.delete');

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
    });
});
