<?php

use App\Domain\Contrato\Contratada\Anexo\Controller\DestroyAnexoContratadaController;
use App\Domain\Contrato\Contratada\Anexo\Controller\StoreAnexoContratadaController;
use App\Domain\Contrato\Contratada\Anexo\Controller\UpdateAnexoContratadaController;
use App\Domain\Contrato\Contratada\Anexo\Controller\VisualizarAnexoContratadaController;
use App\Domain\Contrato\Contratada\app\Controller\ContratoContratadaController;
use App\Domain\Contrato\Contratada\app\Controller\DadosGeraisContratadaController;
use App\Domain\Contrato\Contratada\Empreendimento\Controller\DestroyContratadaEmpreendimentoTrechoController;
use App\Domain\Contrato\Contratada\Empreendimento\Controller\StoreContratadaEmpreendimentoTrechoController;
use App\Domain\Contrato\Contratada\Empreendimento\Controller\UpdateContratadaEmpreendimentoTrechoController;
use app\Domain\Contrato\Contratada\Historico\Controller\DestroyHistoricoContratadaController;
use App\Domain\Contrato\Contratada\Historico\Controller\StoreHistoricoContratadaController;
use App\Domain\Contrato\Contratada\Historico\Controller\UpdateHistoricoContratadaController;
use App\Domain\Contrato\Contratada\Introducao\Controller\StoreIntroducaoContratadaController;
use App\Domain\Contrato\Contratada\Introducao\Controller\UpdateIntroducaoContratadaController;
use App\Domain\Contrato\Contratada\Licenciamento\Controller\DeleteLicenciamentoContratadaController;
use App\Domain\Contrato\Contratada\Licenciamento\Controller\StoreLicenciamentoContratadaController;
use App\Domain\Contrato\Contratada\Licenciamento\Observacao\Controller\DeleteLicenciamentoObservacaoController;
use App\Domain\Contrato\Contratada\Licenciamento\Observacao\Controller\StoreLicenciamentoObservacaoController;
use App\Domain\Contrato\Contratada\Licenciamento\Observacao\Controller\UpdateLicenciamentoObservacaoController;
use app\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\CreateEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\DestroyDocumentoEquipamentoRecursoController;
use App\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\DestroyEquipamentoRecursoController;
use app\Domain\Contrato\Contratada\Recurso\Equipamento\Controller\ListagemEquipamentoRecursoController;
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
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller\VisualizarDocumentoVeiculoRecursoController;
use App\Domain\Contrato\Contratada\Servico\app\Controller\CreateServicosContratadaController;
use App\Domain\Contrato\Contratada\Servico\app\Controller\DeleteServicoContratadaController;
use App\Domain\Contrato\Contratada\Servico\app\Controller\IndexServicosContratadaController;
use App\Domain\Contrato\Contratada\Servico\app\Controller\StoreServicosContratadaController;
use App\Domain\Contrato\Contratada\Servico\app\Controller\UpdateServicosContratadaController;
use App\Domain\Contrato\Contratada\Servico\Condicionante\Controller\DeleteLicencaCondicionanteServicoController;
use App\Domain\Contrato\Contratada\Servico\Condicionante\Controller\StoreLicencaCondicionanteServicoController;
use App\Domain\Contrato\Contratada\Servico\Equipamento\Controller\DeleteServicoEquipamentoContratadaController;
use App\Domain\Contrato\Contratada\Servico\Equipamento\Controller\StoreServicoEquipamentoContratadaController;
use App\Domain\Contrato\Contratada\Servico\Rh\Controller\DeleteServicoRhContratadaController;
use App\Domain\Contrato\Contratada\Servico\Rh\Controller\StoreServicoRhContratadaController;
use App\Domain\Contrato\Contratada\Servico\Veiculo\Controller\DeleteServicoVeiculoContratadaController;
use App\Domain\Contrato\Contratada\Servico\Veiculo\Controller\StoreServicoVeiculoContratadaController;
use App\Domain\Contrato\GestaoContrato\Controller\CreateContratoController;
use App\Domain\Contrato\GestaoContrato\Controller\DestroyContratoController;
use App\Domain\Contrato\GestaoContrato\Controller\DestroyContratoTrechoController;
use App\Domain\Contrato\GestaoContrato\Controller\ExcelExportContratoController;
use App\Domain\Contrato\GestaoContrato\Controller\GetCoordenadaController;
use App\Domain\Contrato\GestaoContrato\Controller\GetGeoJsonController;
use App\Domain\Contrato\GestaoContrato\Controller\ListagemContratoController;
use App\Domain\Contrato\GestaoContrato\Controller\StoreContratoController;
use App\Domain\Contrato\GestaoContrato\Controller\StoreContratoTrechoController;
use App\Domain\Contrato\GestaoContrato\Controller\UpdateContratoController;
use App\Domain\Contrato\GestaoContrato\Controller\UpdateContratoTrechoController;
use App\Domain\Licenca\app\Controller\CreateLicencaController;
use App\Domain\Licenca\app\Controller\GerenciarLicencaController;
use App\Domain\Licenca\app\Controller\GetLicencaController;
use App\Domain\Licenca\app\Controller\IndexController;
use App\Domain\Licenca\app\Controller\SearchLicencaController;
use App\Domain\Licenca\app\Controller\StoreLicencaController;
use App\Domain\Licenca\app\Controller\UpdateLicencaController;
use App\Domain\Licenca\Condicionante\Controller\BuscarLicencaCondicionanteController;
use App\Domain\Licenca\Condicionante\Controller\DestroyCondicionanteController;
use App\Domain\Licenca\Condicionante\Controller\ListagemCondicionanteController;
use App\Domain\Licenca\Condicionante\Controller\StoreCondicionanteController;
use App\Domain\Licenca\Condicionante\Controller\StoreImportacaoCondicionanteController;
use App\Domain\Licenca\Condicionante\Controller\UpdateCondicionanteController;
use App\Domain\Licenca\Documento\Controller\VisualizarDocumentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\DeleteLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\GetUFLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\StoreLicencaSegmentoController;
use App\Domain\Licenca\LicencaSegmento\Controller\UpdateLicencaSegmentoController;
use App\Domain\Licenca\Requerimento\Controller\DestroyRequerimentoController;
use App\Domain\Licenca\Requerimento\Controller\StoreRequerimentoController;
use App\Domain\Licenca\Requerimento\Controller\visualizarRequerimentoController;
use App\Domain\Licenca\Trecho\Controller\GetCoordenadaTrechoController;
use App\Shared\Base\Profile\ProfileController;
use App\Shared\Base\Role\Controllers\RoleController;
use App\Shared\Base\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Licença
// Licença Segmento
// Licença Condicionante
// Licença Requerimento
// Licença Trecho
// Licença Documetno
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Dashboard Redirect
Route::get('/', function () {

    session()->reflash();

    $user = auth()->user();

    if ($user && !$user->can('dashboard')) {
        return to_route('profile.edit');
    }

    return to_route('dashboard');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // Somente usuários com perfil que tenha as permissões adequadas
    Route::middleware('route-permission')->group(function () {

        // Dashboard (Home page)
        Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');

        // Ambiente Geo
        Route::get('/ambienteGeo', fn () => Inertia::render('AmbienteGeo'))->name('ambienteGeo');

        // Cadastros
        Route::prefix('cadastros')->group(function () {

            // Usuários
            Route::prefix('usuarios')->group(function () {
                Route::get('/',                     [UserController::class, 'index'])->name('cadastros.usuarios.listagem');
                Route::get('/formulario/{user?}',   [UserController::class, 'create'])->name('cadastros.usuarios.formulario');
                Route::post('/criar',               [UserController::class, 'store'])->name('cadastros.usuarios.criar');
                Route::patch('/atualizar/{user}',   [UserController::class, 'update'])->name('cadastros.usuarios.atualizar');
                Route::delete('/deletar/{user}',    [UserController::class, 'destroy'])->name('cadastros.usuarios.deletar');
            }); // FIM USUARIOS

            // Perfis
            Route::prefix('perfis')->group(function () {
                Route::get('/',                     [RoleController::class, 'index'])->name('cadastros.perfis.listagem');
                Route::get('/formulario/{role?}',   [RoleController::class, 'create'])->name('cadastros.perfis.formulario');
                Route::post('/criar',               [RoleController::class, 'store'])->name('cadastros.perfis.criar');
                Route::patch('/atualizar/{role}',   [RoleController::class, 'update'])->name('cadastros.perfis.atualizar');
                Route::delete('/deletar/{role}',    [RoleController::class, 'destroy'])->name('cadastros.perfis.deletar');
            });
        });

        // Contratos
        Route::prefix('contratos')->group(function () {

            // Busca Contrato SIMDNIT
            Route::get('/contrato/{nr_contrato}', [App\Domain\Contrato\GestaoContrato\Controller\GetContratoDnitController::class, 'getContrato'])
                ->name('contratos.get_contrato');

            // Gestão de Contrato
            Route::prefix('gestao')->group(function () {

                Route::get('/{tipo}',                           [ListagemContratoController::class,         'index'])->name('contratos.gestao.listagem');
                Route::get('/create/{tipo?}/{contrato?}',       [CreateContratoController::class,           'create'])->name('contratos.gestao.create');
                Route::post('/store',                           [StoreContratoController::class,            'store'])->name('contratos.gestao.store');
                Route::patch('/atualizar/{contrato}',           [UpdateContratoController::class,           'update'])->name('contratos.gestao.atualizar');
                Route::get('/delete/{contrato}',                [DestroyContratoController::class,          'destroy'])->name('contratos.gestao.delete');
                Route::get('/excel',                            [ExcelExportContratoController::class,      'excelExport'])->name('contratos.gestao.excel_export');
                Route::post('/get_coordenada',                  [GetCoordenadaController::class,            'getCoordenada'])->name('contratos.gestao.get_coordenada');
                Route::post('/get_geo_json',                    [GetGeoJsonController::class,               'getGeoJson'])->name('contratos.gestao.get_geo_json');
                Route::post('/store_trecho',                    [StoreContratoTrechoController::class,      'storeTrecho'])->name('contratos.gestao.store_trecho');
                Route::patch('/atualizar_trecho/{trecho}',      [UpdateContratoTrechoController::class,     'updateTrecho'])->name('contratos.gestao.update_trecho');
                Route::delete('/delete_trecho/{tipo}/{trecho}', [DestroyContratoTrechoController::class,    'destroyTrecho'])->name('contratos.gestao.delete_trecho');
            });

            Route::prefix('/contratada')->group(function () {
                Route::get('{contrato}/',                                       [ContratoContratadaController::class,                       'index'])->name('contratos.contratada.index');
                Route::get('{contrato}/dados_gerais',                           [DadosGeraisContratadaController::class,                    'index'])->name('contratos.contratada.dados_gerais.index');
                Route::post('/store_introducao',                                [StoreIntroducaoContratadaController::class,                'index'])->name('contratos.contratada.store_introducao.index');
                Route::patch('/update_introducao/{introducao}',                 [UpdateIntroducaoContratadaController::class,               'index'])->name('contratos.contratada.update_introducao.index');
                Route::post('/store_licenciamento',                             [StoreLicenciamentoContratadaController::class,             'index'])->name('contratos.contratada.store_licenciamento');
                Route::post('/delete_licenciamento/{licenca}',                  [DeleteLicenciamentoContratadaController::class,            'index'])->name('contratos.contratada.delete_licenciamento');
                Route::post('/store_licenciamento_observacao',                  [StoreLicenciamentoObservacaoController::class,             'index'])->name('contratos.contratada.store_licenciamento_observacao');
                Route::patch('/update_licenciamento_observacao/{observacao}',   [UpdateLicenciamentoObservacaoController::class,            'index'])->name('contratos.contratada.update_licenciamento_observacao');
                Route::delete('/delete_licenciamento_observacao/{observacao}',  [DeleteLicenciamentoObservacaoController::class,            'index'])->name('contratos.contratada.delete_licenciamento_observacao');
                Route::post('/store_historico',                                 [StoreHistoricoContratadaController::class,                 'index'])->name('contratos.contratada.store_historico');
                Route::patch('/update_historico',                               [UpdateHistoricoContratadaController::class,                'index'])->name('contratos.contratada.update_historico');
                Route::delete('/destroy_historico/{historico}',                 [DestroyHistoricoContratadaController::class,               'index'])->name('contratos.contratada.destroy_historico');
                Route::post('/store_anexo',                                     [StoreAnexoContratadaController::class,                     'index'])->name('contratos.contratada.store_anexo');
                Route::post('/update_anexo',                                    [UpdateAnexoContratadaController::class,                    'index'])->name('contratos.contratada.update_anexo');
                Route::delete('/destroy_anexo/{anexo}',                         [DestroyAnexoContratadaController::class,                   'index'])->name('contratos.contratada.destroy_anexo');
                Route::get('/visualizar/{anexo}',                               [VisualizarAnexoContratadaController::class,                'index'])->name('contratos.contratada.visualizar_anexo');
                Route::post('/store_empreendimento_trecho',                     [StoreContratadaEmpreendimentoTrechoController::class,      'index'])->name('contratos.contratada.store_empreendimento_trecho');
                Route::patch('/update_empreendimento_trecho',                   [UpdateContratadaEmpreendimentoTrechoController::class,     'index'])->name('contratos.contratada.update_empreendimento_trecho');
                Route::delete('/destroy_empreendimento_trecho/{trecho}',        [DestroyContratadaEmpreendimentoTrechoController::class,    'index'])->name('contratos.contratada.destroy_empreendimento_trecho');

                Route::get('{contrato}/recurso/equipamento',                            [ListagemEquipamentoRecursoController::class,               'index'])->name('contratos.contratada.recurso.equipamento.index');
                Route::get('{contrato}/recurso/equipamento/create/{equipamento?}',      [CreateEquipamentoRecursoController::class,                 'index'])->name('contratos.contratada.recurso.equipamento.create');
                Route::post('recurso/equipamento/store',                                [StoreEquipamentoRecursoController::class,                  'index'])->name('contratos.contratada.recurso.equipamento.store');
                Route::post('recurso/equipamento/store_documento',                      [StoreDocumentoEquipamentoRecursoController::class,         'index'])->name('contratos.contratada.recurso.equipamento.store_documento');
                Route::get('recurso/equipamento/visualizar/{documento}',                [VisualizarDocumentoEquipamentoRecursoController::class,    'index'])->name('contratos.contratada.recurso.equipamento.visualizar');
                Route::delete('{contrato}/recurso/equipamento/destroy/{documento}',     [DestroyDocumentoEquipamentoRecursoController::class,       'index'])->name('contratos.contratada.recurso.equipamento.destroy');
                Route::delete('recurso/equipamento/destroy_equipamento/{equipamento}',  [DestroyEquipamentoRecursoController::class,                'index'])->name('contratos.contratada.recurso.equipamento.destroy_equipamento');

                Route::get('{contrato}/recurso/veiculo',                        [ListagemVeiculoRecursoController::class,               'index'])->name('contratos.contratada.recurso.veiculo.index');
                Route::get('{contrato}/recurso/veiculo/create/{veiculo?}',      [CreateVeiculoRecursoController::class,                 'index'])->name('contratos.contratada.recurso.veiculo.create');
                Route::post('recurso/veiculo/store',                            [StoreVeiculoRecursoController::class,                  'index'])->name('contratos.contratada.recurso.veiculo.store');
                Route::post('recurso/veiculo/store_documento',                  [StoreDocumentoVeiculoRecursoController::class,         'index'])->name('contratos.contratada.recurso.veiculo.store_documento');
                Route::get('recurso/veiculo/visualizar/{documento}',            [VisualizarDocumentoVeiculoRecursoController::class,    'index'])->name('contratos.contratada.recurso.veiculo.visualizar');
                Route::delete('{contrato}/recurso/veiculo/destroy/{documento}', [DestroyDocumentoVeiculoRecursoController::class,       'index'])->name('contratos.contratada.recurso.veiculo.destroy');
                Route::delete('recurso/veiculo/destroy_veiculo/{veiculo}',      [DestroyVeiculoRecursoController::class,                'index'])->name('contratos.contratada.recurso.veiculo.destroy_veiculo');

                Route::get('{contrato}/recurso/rh',                         [ListagemRhRecursoController::class,            'index'])->name('contratos.contratada.recurso.rh.index');
                Route::get('{contrato}/recurso/rh/create/{rh?}',            [CreateRhRecursoController::class,              'index'])->name('contratos.contratada.recurso.rh.create');
                Route::post('recurso/rh/store',                             [StoreRhRecursoController::class,               'index'])->name('contratos.contratada.recurso.rh.store');
                Route::patch('recurso/rh/update/{rh}',                      [UpdateRhRecursoController::class,              'index'])->name('contratos.contratada.recurso.rh.update');
                Route::post('recurso/rh/store_documento',                   [StoreDocumentoRhRecursoController::class,      'index'])->name('contratos.contratada.recurso.rh.store_documento');
                Route::get('recurso/rh/visualizar/{documento}',             [VisualizarDocumentoRhRecursoController::class, 'index'])->name('contratos.contratada.recurso.rh.visualizar');
                Route::delete('{contrato}/recurso/rh/destroy/{documento}',  [DestroyDocumentoRhRecursoController::class,    'index'])->name('contratos.contratada.recurso.rh.destroy');
                Route::delete('recurso/rh/destroy_rh/{rh}',                 [DestroyRhRecursoController::class,             'index'])->name('contratos.contratada.recurso.rh.destroy_rh');

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
        }); // FIM CONTRATOS

        // Licenças
        Route::prefix('licenca')->group(function () {
            // Licença
            Route::get('/',                   [IndexController::class,         'index'])->name('licenca.index');
            Route::get('/arquivo/{arquivo}',  [IndexController::class,         'index'])->name('licenca.arquivo');
            Route::get('/create/{licenca?}',  [CreateLicencaController::class, 'index'])->name('licenca.create');
            Route::post('/store',             [StoreLicencaController::class,  'index'])->name('licenca.store');
            Route::patch('/update/{licenca}', [UpdateLicencaController::class, 'index'])->name('licenca.update');
            Route::get('/search/{licenca?}',  [SearchLicencaController::class, 'index'])->name('licenca.search')->where('licenca', '(.*)');
            Route::patch('/gerenciar-licenca/{licenca}', [GerenciarLicencaController::class, 'index'])->name('licenca.gerenciar-licenca');

            // Licença Segmento
            Route::post('/store_segmento',               [StoreLicencaSegmentoController::class,  'index'])->name('licenca_segmento.store');
            Route::patch('/update_segmento/{segmento}',  [UpdateLicencaSegmentoController::class, 'index'])->name('licenca_segmento.update');
            Route::delete('/delete_segmento/{segmento}', [DeleteLicencaSegmentoController::class, 'index'])->name('licenca_segmento.delete');
            Route::get('/get_uf_segmento',               [GetUFLicencaSegmentoController::class,  'index'])->name('licenca_segmento.get_uf');
            Route::post('/get_licenca',                  [GetLicencaController::class,            'index'])->name('licenca.get_licenca');
            // Trecho
            Route::prefix('trecho')->group(function () {
                Route::post('/get_coordenada_trecho', [GetCoordenadaTrechoController::class, 'index'])->name('licenca.trecho.get_coordenada_trecho');
            });
            // Condicionante
            Route::prefix('condicionante')->group(function () {
                Route::get('/{licenca}',                [ListagemCondicionanteController::class,        'index'])->name('licenca.condicionante.index');
                Route::post('/buscar_licenca',          [BuscarLicencaCondicionanteController::class,   'index'])->name('licenca.condicionante.buscar_licenca');
                Route::post('/store',                   [StoreCondicionanteController::class,           'index'])->name('licenca.condicionante.store');
                Route::post('/store_importacao',        [StoreImportacaoCondicionanteController::class, 'index'])->name('licenca.condicionante.store_importacao');
                Route::patch('/update/{condicionante}', [UpdateCondicionanteController::class,          'index'])->name('licenca.condicionante.update');
                Route::get('/destroy/{condicionante}',  [DestroyCondicionanteController::class,         'index'])->name('licenca.condicionante.destroy');
            });
            // Documento
            Route::prefix('documento')->group(function () {
                Route::get('/visualizar/{documento}', [VisualizarDocumentoController::class, 'index'])->name('licenca.documento.visualizar');
            });
            // Requerimento
            Route::prefix('requerimento')->group(function () {
                Route::post('/store',                    [StoreRequerimentoController::class,      'index'])->name('licenca.requerimento.store');
                Route::get('/visualizar/{requerimento}', [visualizarRequerimentoController::class, 'index'])->name('licenca.requerimento.visualizar');
                Route::delete('/destroy/{requerimento}', [DestroyRequerimentoController::class,    'index'])->name('licenca.requerimento.destroy');
            });
        }); // FIM LICENÇAS

        Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('logs');
    });
});

require __DIR__ . '/auth.php';