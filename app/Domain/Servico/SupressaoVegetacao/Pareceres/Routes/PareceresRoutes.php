<?php

use App\Domain\Servico\SupressaoVegetacao\Pareceres\Controller\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/{contrato}/{servico}', IndexController::class)->name('contratos.contratada.servicos.supressao-vegetacao.pareceres.index');

