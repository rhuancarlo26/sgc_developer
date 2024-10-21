<?php

use App\Domain\Fiscal\Parecer\Controllers\EmiteParecerServicoController;
use App\Domain\Fiscal\Parecer\Controllers\EmiteParecerConfigPMQAController;
use App\Domain\Fiscal\Parecer\Controllers\EmiteParecerConfigSupressaoController;
use App\Domain\Fiscal\Parecer\Controllers\EmiteParecerConfigAfugentamentoController;
use App\Domain\Fiscal\Parecer\Controllers\EmiteParecerConfigSupervisaoController;
use Illuminate\Support\Facades\Route;

Route::post('/emite-parecer-servico',              [EmiteParecerServicoController::class,             'index'])->name('fiscal.emite-parecer-servico');
Route::post('/emite-parecer-config-pmqa',          [EmiteParecerConfigPMQAController::class,          'index'])->name('fiscal.emite-parecer-config-pqma');
Route::post('/emite-parecer-config-supressao',     [EmiteParecerConfigSupressaoController::class,     'index'])->name('fiscal.emite-parecer-config-supressao');
Route::post('/emite-parecer-config-supervisao',    [EmiteParecerConfigSupervisaoController::class,     'index'])->name('fiscal.emite-parecer-config-supervisao');
Route::post('/emite-parecer-config-afugentamento', [EmiteParecerConfigAfugentamentoController::class, 'index'])->name('fiscal.emite-parecer-config-afugentamento');
