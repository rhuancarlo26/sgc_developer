<?php

use App\Domain\Dashboard\MonAtpFauna\Controller\IndexDashboardMonAtpFaunaController;
use App\Domain\Dashboard\MonitoraFauna\Controller\IndexDashboardMonitoraFaunaController;
use App\Domain\Dashboard\PassagemFauna\Controller\IndexDashboardPassagemFaunaController;
use App\Domain\Dashboard\AfugentamentoFauna\Controller\IndexDashboardAfugentamentoFaunaController;
use App\Domain\Dashboard\app\controller\IndexController;
use App\Domain\Dashboard\PMQA\Controller\IndexDashboardPMQAController;
use App\Domain\Dashboard\SupervisaoAmbiental\Controller\IndexDashboardSupervisaoAmbientalController;
use App\Domain\Dashboard\SupressaoVegetal\Controller\IndexDashboardSupressaoVegetalController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    require __DIR__ . '/Base/Profile.php';

    // Somente usuários com perfil que tenha as permissões adequadas
    Route::middleware('route-permission')->group(function () {
        // Usuarios
        require __DIR__ . '/Base/Users.php';

        // Contratos: Gestão de contratos e contratada
        require __DIR__ . '/../app/Domain/Contrato/Routes/ContratosRoutes.php';

        // Licenças
        require __DIR__ . '/../app/Domain/Licenca/app/Router/LicencasRouter.php';

        // Fiscal
        require __DIR__ . '/../app/Domain/Fiscal/app/Routes/FiscalRouter.php';

        // Dashboard (Home page)
        Route::get('/dashboard', [IndexController::class, 'index'])->name('dashboard');

        Route::prefix('dashboard')->group(function () {
            Route::prefix('pmqa')->group(function () {
                Route::get('/{servicos}', [IndexDashboardPMQAController::class, 'index'])->name('dashboard.pmqa');
            });
            Route::prefix('mon-atp-fauna')->group(function () {
                Route::get('/{servicos}', [IndexDashboardMonAtpFaunaController::class, 'index'])->name('dashboard.mon-atp-fauna');
            });
            Route::prefix('passagem-fauna')->group(function () {
                Route::get('/', [IndexDashboardPassagemFaunaController::class, 'index'])->name('dashboard.passagem-fauna');
            });
            Route::prefix('monitora-fauna')->group(function () {
                Route::get('/{servicos}', [IndexDashboardMonitoraFaunaController::class, 'index'])->name('dashboard.monitora-fauna');
            });
            Route::prefix('afugentamentoFauna')->group(function () {
                Route::get('/', [IndexDashboardAfugentamentoFaunaController::class, 'index'])->name('dashboard.afugentamentoFauna');
            });
            Route::prefix('supressaoVegetal')->group(function () {
                Route::get('/', [IndexDashboardSupressaoVegetalController::class, 'index'])->name('dashboard.supressaoVegetal');
            });
            Route::prefix('supervisaoAmbiental')->group(function () {
                Route::get('/', [IndexDashboardSupervisaoAmbientalController::class, 'index'])->name('dashboard.supervisaoAmbiental');
            });
        });

        // Ambiente Geo
        Route::get('/ambienteGeo', fn() => Inertia::render('AmbienteGeo'))->name('ambienteGeo');

        Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('logs');

        // SGC
        require __DIR__ . '/../app/Domain/Sgc/Routes/SgcRoutes.php';

        Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('logs');
    });
});

require __DIR__ . '/auth.php';
