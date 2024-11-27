<?php

use App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Controller\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio.index');
