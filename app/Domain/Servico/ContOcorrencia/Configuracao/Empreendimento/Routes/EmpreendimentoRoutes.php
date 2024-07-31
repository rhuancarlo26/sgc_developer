<?php

use App\Domain\Servico\ContOcorrencia\Configuracao\Empreendimento\Controller\IndexController;
use App\Domain\Servico\ContOcorrencia\Configuracao\Empreendimento\Controller\StoreLicencaShapefileController;
use Illuminate\Support\Facades\Route;

Route::get('{contrato}/{servico}/', [IndexController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.configuracao.empreendimento.index');
Route::post('{contrato}/{servico}/store_licenca_shapefile', [StoreLicencaShapefileController::class, 'index'])->name('contratos.contratada.servicos.cont_ocorrencia.configuracao.empreendimento.store_licenca_shapefile');
