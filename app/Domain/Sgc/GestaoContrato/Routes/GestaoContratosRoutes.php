<?php
use Illuminate\Support\Facades\Route;
use App\Domain\Sgc\GestaoContrato\Controller\ListagemSgcController;
use App\Domain\Sgc\GestaoContrato\Controller\ListagemContratoController;




Route::prefix('gestao')->group(function () {
    Route::get('/{tipo}',                           [ListagemContratoController::class,      'index'])->name('sgc.gestao.listagem');
   
});
