<?php

use App\Domain\Modulos\ConfiguracoesModulos\Controllers\ConfiguracoesModulosController;
use App\Domain\Modulos\ConfiguracoesModulos\Controllers\CreateConfigModuloController;
use App\Domain\Modulos\ConfiguracoesModulos\Controllers\StoreConfigModuloController;
use App\Domain\Modulos\ConfiguracoesModulos\Controllers\UpdateConfigModuloController;
use App\Domain\Modulos\ConfiguracoesModulos\Controllers\ProcessarCamposPlanilhaController;
use App\Domain\Modulos\ConfiguracoesModulos\Controllers\GerarPlanilhaModeloController;
use App\Domain\Modulos\ConfiguracoesModulos\Controllers\DeleteConfigModuloController;
use Illuminate\Support\Facades\Route;

Route::prefix('configuracoes-modulos')->group(function () {

    Route::get('/', [ConfiguracoesModulosController::class, 'index'])->name('config-modulos.index');

    Route::get('/formulario/{modulo?}', [CreateConfigModuloController::class, 'index'])->name('config-modulos.formulario');
    Route::post('/processar-campos-planilha', [ProcessarCamposPlanilhaController::class, 'processarCamposPlanilha'])->name('config-modulos.processar-campos-planilha');
    Route::post('/formulario', [StoreConfigModuloController::class, 'store'])->name('config-modulos.store');
    Route::post('/formulario/atualizar/{modulo?}', [UpdateConfigModuloController::class, 'update'])->name('config-modulos.update');
    Route::delete('/formulario/excluir/{modulo?}', [DeleteConfigModuloController::class, 'delete'])->name('config-modulos.delete');

    Route::get('gerar-planilha-modelo/{modulo}', [GerarPlanilhaModeloController::class, 'gerarPlanilhaModelo'])->name('config-modulos.gerar-planilha-modelo');
});
