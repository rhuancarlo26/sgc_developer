<?php

use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Controller\PatrimonioController;
use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Controller\StorePatrimonioShapefileController;
use Illuminate\Support\Facades\Route;

// Rota de teste GET
Route::get('/teste', function() {
    return response()->json([
        'success' => true,
        'message' => 'Rota do patrimônio está funcionando!',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

// Sua rota POST original
Route::post('/store', [PatrimonioController::class, 'store'])->name('patrimonio.store');
Route::post('/shapefile', StorePatrimonioShapefileController::class)->name('patrimonio.shapefile.store');
