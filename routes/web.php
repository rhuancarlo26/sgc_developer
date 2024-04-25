<?php

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
        require __DIR__ . '/Licencas/Licencas.php';

        // Dashboard (Home page)
        Route::get('/dashboard', fn() => Inertia::render('Dashboard'))->name('dashboard');

        // Ambiente Geo
        Route::get('/ambienteGeo', fn() => Inertia::render('AmbienteGeo'))->name('ambienteGeo');

        Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('logs');
    });
});

require __DIR__ . '/auth.php';
