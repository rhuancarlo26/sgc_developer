<?php

use App\Domain\Profile\ProfileController;
use App\Domain\Role\Controllers\RoleController;
use App\Domain\User\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
                Route::get('/create/{tipo}/{contrato?}', [App\Domain\Contrato\GestaoContrato\Controller\CreateContratoController::class, 'create'])
                    ->name('contratos.gestao.create');
                Route::post('/store', [App\Domain\Contrato\GestaoContrato\Controller\StoreContratoController::class, 'store'])
                    ->name('contratos.gestao.store');
                Route::patch('/atualizar/{contrato}', [App\Domain\Contrato\GestaoContrato\Controller\UpdateContratoController::class, 'update'])
                    ->name('contratos.gestao.atualizar');
                Route::delete('/delete/{contrato?}', [App\Domain\Contrato\GestaoContrato\Controller\DestroyContratoController::class, 'destroy'])
                    ->name('contratos.gestao.delete');
                Route::get('/excel', [App\Domain\Contrato\GestaoContrato\Controller\ExcelExportContratoController::class, 'excelExport'])
                    ->name('contratos.gestao.excel_export');
                Route::post('/get_coordenada', [App\Domain\Contrato\GestaoContrato\Controller\GetCoordenadaController::class, 'getCoordenada'])
                    ->name('contratos.gestao.get_coordenada');
                Route::post('/get_geo_json', [App\Domain\Contrato\GestaoContrato\Controller\GetGeoJsonController::class, 'getGeoJson'])
                    ->name('contratos.gestao.get_geo_json');
                Route::post('/store_trecho', [App\Domain\Contrato\GestaoContrato\Controller\StoreTrechoContratoController::class, 'storeTrecho'])
                    ->name('contratos.gestao.store_trecho');
                Route::patch('/atualizar_trecho/{trecho}', [App\Domain\Contrato\GestaoContrato\Controller\UpdateTrechoContratoController::class, 'updateTrecho'])
                    ->name('contratos.gestao.update_trecho');
                Route::delete('/delete_trecho/{tipo}/{trecho}', [App\Domain\Contrato\GestaoContrato\Controller\DestroyTrechoContratoController::class, 'destroyTrecho'])
                    ->name('contratos.gestao.delete_trecho');
            });
        });


        Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])
            ->name('logs');
    });
});

require __DIR__ . '/auth.php';