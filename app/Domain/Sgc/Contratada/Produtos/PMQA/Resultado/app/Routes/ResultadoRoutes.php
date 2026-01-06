<?php

use App\Domain\Dashboard\PMQA\Controller\ResultadoPMQAController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller\IndexController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller\StoreController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller\UpdateController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller\DeleteController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller\DeleteOutraAnaliseController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller\ResultadoController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller\StoreAnaliseController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller\StoreAnaliseIqaController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller\StoreOutraAnaliseController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller\UpdateAnaliseController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller\UpdateAnaliseIqaController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller\UpdateOutraAnaliseController;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller\VisualizarOutraAnaliseController;
use Illuminate\Support\Facades\Route;

Route::prefix('/resultado')->group(function () {
  Route::get('{servico}/',                                                     [IndexController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.index');
  Route::post('{servico}/store',                                               [StoreController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.store');
  Route::patch('{servico}/update',                                             [UpdateController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.update');
  Route::delete('{servico}/delete/{resultado}',                                [DeleteController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.delete');
  Route::get('{servico}/resultado/{resultado}',                                [ResultadoController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.resultado');
  Route::get('{servico}/resultadoGet/{resultado}',                             [ResultadoPMQAController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.resultado.get');
  Route::post('{servico}/{resultado}/store_analise',                           [StoreAnaliseController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.store_analise');
  Route::post('{servico}/{resultado}/store_analise_iqa',                       [StoreAnaliseIqaController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.store_analise_iqa');
  Route::post('{servico}/{resultado}/store_outra_analise',                     [StoreOutraAnaliseController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.store_outra_analise');
  Route::post('{servico}/{resultado}/update_outra_analise',                    [UpdateOutraAnaliseController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.update_outra_analise');
  Route::post('{servico}/{resultado}/update_analise',                          [UpdateAnaliseController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.update_analise');
  Route::post('{servico}/{resultado}/update_analise_iqa',                      [UpdateAnaliseIqaController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.update_analise_iqa');
  Route::get('{servico}/{resultado}/visualizar_outra_analise/{outra_analise}', [VisualizarOutraAnaliseController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.visualizar_outra_analise');
  Route::delete('{servico}/{resultado}/delete_outra_analise/{outra_analise}',  [DeleteOutraAnaliseController::class, 'index'])->name('contratos.contratada.servicos.pmqa.resultado.delete_outra_analise');
});

