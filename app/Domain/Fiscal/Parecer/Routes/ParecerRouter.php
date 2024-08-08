<?php

use App\Domain\Fiscal\Parecer\Controllers\EmiteParecerServicoController;
use App\Domain\Fiscal\Parecer\Controllers\EmiteParecerConfigPMQAController;
use Illuminate\Support\Facades\Route;

Route::post('/emite-parecer-servico',     [EmiteParecerServicoController::class, 'index'])->name('fiscal.emite-parecer-servico');
Route::post('/emite-parecer-config-pmqa', [EmiteParecerConfigPMQAController::class, 'index'])->name('fiscal.emite-parecer-config-pqma');
