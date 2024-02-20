<?php

use App\Shared\Base\Profile\ProfileController;
use App\Shared\Base\Role\Controllers\RoleController;
use App\Shared\Base\User\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Licença
use App\Domain\Licenca\Controller\ListagemLicencaController;
use App\Domain\Licenca\Controller\CreateLicencaController;
use App\Domain\Licenca\Controller\StoreLicencaController;
use App\Domain\Licenca\Controller\UpdateLicencaController;

// Dashboard Redirect
Route::get('/', function () {

    session()->reflash();

    $user = auth()->user();

    if ($user && !$user->can('dashboard')) {
        return to_route('profile.edit');
    }

    return to_route('dashboard');
});

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
        Route::get('/dashboard', fn () => Inertia::render('Dashboard'))
            ->name('dashboard');

        // Ambiente Geo
        Route::get('/ambienteGeo', fn() => Inertia::render('AmbienteGeo'))->name('ambienteGeo');

        // Cadastros
        Route::prefix('cadastros')->group(function () {

            // Usuários
            Route::prefix('usuarios')->group(function () {

                Route::get('/', [UserController::class, 'index'])
                    ->name('cadastros.usuarios.listagem');
                Route::get('/formulario/{user?}', [UserController::class, 'create'])
                    ->name('cadastros.usuarios.formulario');
                Route::post('/criar', [UserController::class, 'store'])
                    ->name('cadastros.usuarios.criar');
                Route::patch('/atualizar/{user}', [UserController::class, 'update'])
                    ->name('cadastros.usuarios.atualizar');
                Route::delete('/deletar/{user}', [UserController::class, 'destroy'])
                    ->name('cadastros.usuarios.deletar');
            });

            // Perfis
            Route::prefix('perfis')->group(function () {

                Route::get('/', [RoleController::class, 'index'])
                    ->name('cadastros.perfis.listagem');
                Route::get('/formulario/{role?}', [RoleController::class, 'create'])
                    ->name('cadastros.perfis.formulario');
                Route::post('/criar', [RoleController::class, 'store'])
                    ->name('cadastros.perfis.criar');
                Route::patch('/atualizar/{role}', [RoleController::class, 'update'])
                    ->name('cadastros.perfis.atualizar');
                Route::delete('/deletar/{role}', [RoleController::class, 'destroy'])
                    ->name('cadastros.perfis.deletar');
            });
        });

        // Contratos
        Route::prefix('contratos')->group(function () {

            // Busca Contrato SIMDNIT
            Route::get('contrato/{nr_contrato}', fn (Request $request) => Http::retry(3, 1000)->withoutVerifying()->get("https://servicos.dnit.gov.br/DPP/api/contrato/dnit/{$request->nr_contrato}")->json('data'))->name('contratos.get_contrato');

            // Gestão de Contrato
            Route::prefix('gestao')->group(function () {

                Route::get('/{tipo}', [App\Domain\Contrato\GestaoContrato\Controller\ListagemContratoController::class, 'index'])
                    ->name('contratos.gestao.listagem');
                Route::get('/create/{tipo?}/{contrato?}', [App\Domain\Contrato\GestaoContrato\Controller\CreateContratoController::class, 'create'])
                    ->name('contratos.gestao.create');
                Route::post('/store', [App\Domain\Contrato\GestaoContrato\Controller\StoreContratoController::class, 'store'])
                    ->name('contratos.gestao.store');
                Route::patch('/atualizar/{contrato}', [App\Domain\Contrato\GestaoContrato\Controller\UpdateContratoController::class, 'update'])
                    ->name('contratos.gestao.atualizar');
                Route::get('/delete/{contrato}', [App\Domain\Contrato\GestaoContrato\Controller\DestroyContratoController::class, 'destroy'])
                    ->name('contratos.gestao.delete');
                Route::get('/excel', [App\Domain\Contrato\GestaoContrato\Controller\ExcelExportContratoController::class, 'excelExport'])
                    ->name('contratos.gestao.excel_export');
                Route::post('/get_coordenada', [App\Domain\Contrato\GestaoContrato\Controller\GetCoordenadaController::class, 'getCoordenada'])
                    ->name('contratos.gestao.get_coordenada');
                Route::post('/get_geo_json', [App\Domain\Contrato\GestaoContrato\Controller\GetGeoJsonController::class, 'getGeoJson'])
                    ->name('contratos.gestao.get_geo_json');
                Route::post('/store_trecho', [App\Domain\Contrato\GestaoContrato\Controller\StoreContratoTrechoController::class, 'storeTrecho'])
                    ->name('contratos.gestao.store_trecho');
                Route::patch('/atualizar_trecho/{trecho}', [App\Domain\Contrato\GestaoContrato\Controller\UpdateContratoTrechoController::class, 'updateTrecho'])
                    ->name('contratos.gestao.update_trecho');
                Route::delete('/delete_trecho/{tipo}/{trecho}', [App\Domain\Contrato\GestaoContrato\Controller\DestroyContratoTrechoController::class, 'destroyTrecho'])
                    ->name('contratos.gestao.delete_trecho');
            });
        });

        // Licenças
        Route::prefix('licenca')->group(function () {
            Route::get('/',                     [ListagemLicencaController::class,  'index'])->name('licenca.index');
            Route::get('/create/{licenca?}',    [CreateLicencaController::class,    'index'])->name('licenca.create');
            Route::post('/store',               [StoreLicencaController::class,     'index'])->name('licenca.store');
            Route::patch('/update/{licenca}',   [UpdateLicencaController::class,    'index'])->name('licenca.update');
        });


        Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])
            ->name('logs');
    });
});

require __DIR__ . '/auth.php';
