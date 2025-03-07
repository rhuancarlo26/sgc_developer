<?php

use App\Domain\Sgc\Contratada\app\Controller\CreateEmpreendimento;
use App\Domain\Sgc\Contratada\app\Controller\DashplanController;
use App\Domain\Sgc\Contratada\app\Controller\EmpreendimentosController;
use App\Domain\Sgc\Contratada\app\Controller\UpdateEmpreendimentoController;
use App\Domain\Sgc\Contratada\Coordenadas\CoordenadasController;
use App\Domain\Sgc\Contratada\Dav\Controller\ListagemDavController;
use App\Domain\Sgc\Contratada\Dav\Controller\StoreDav;
use App\Domain\Sgc\Contratada\Dav\Controller\StoreDavProfissionais;
use Illuminate\Support\Facades\Route;
use App\Domain\Sgc\GestaoContrato\Controller\ListagemContratoController;
use \App\Domain\Sgc\Contratada\Dav\Controller\DavConsumoController;



Route::prefix('gestao')->group(function () {
  Route::get('/{tipo}',                                       [ListagemContratoController::class,                      'index'])->name('sgc.gestao.listagem');
  Route::get('{tipo}/dashboard',                              [DashplanController::class,                              'index'])->name('sgc.gestao.dashboard.index');
  Route::post('{tipo}/dashboard-searchempreendimentos',       [DashplanController::class,                              'searchempreendimentos'])->name('sgc.gestao.dashboard.searchempreendimentos');
  Route::post('{tipo}/dashboard-novafase',                    [DashplanController::class,                              'novafase'])->name('sgc.gestao.dashboard.novafase');
  Route::post('{tipo}/dashboard-updatefase',                  [DashplanController::class,                              'updatefase'])->name('sgc.gestao.dashboard.updatefase');
  Route::post('{tipo}/dashboard-search',                      [DashplanController::class,                              'search'])->name('sgc.gestao.dashboard.search');
  Route::post('{tipo}/dashboard-import',                      [DashplanController::class,                              'import'])->name('sgc.gestao.dashboard.import');
  Route::get('{tipo}/dashboard/{empreendimento}',             [EmpreendimentosController::class,                       'index'])->name('sgc.gestao.dashboard.empreendimento.index');
  Route::get('/dashboard/createEmpreendimento/{tipo?}/{id?}', [CreateEmpreendimento::class,                            'create'])->name('sgc.gestao.dashboard.empreendimento.create');
  Route::post('/dashboard/salvarEmpreendimento',              [UpdateEmpreendimentoController::class,                  'updateEmpreendimento'])->name('sgc.gestao.dashboard.empreendimento.store');
  Route::delete('/dashboard/delete-empreendimento/{id}',      [EmpreendimentosController::class,                       'deleteEmpreendimento'])->name('sgc.gestao.dashboard.empreendimento.delete');
  Route::get('/',                                             [CoordenadasController::class,                           'getGeoJson'])->name('sgc.gestao.dashboard.geojson');
  Route::get('dav/historicos/{contrato}',                                [ListagemDavController::class,                           'historicos'])->name('sgc.gestao.historicos');
  Route::get('dav/{contrato}',                                [ListagemDavController::class,                           'index'])->name('sgc.gestao.listagemDav');
  Route::post('dav/storeDav',                                 [StoreDav::class,                                        'index'])->name('sgc.gestao.storeDav');
  Route::get('dav/{contrato}/{id}',                           [ListagemDavController::class,                           'details'])->name('sgc.gestao.details');
  Route::post('dav/update/{contrato}',                        [DavConsumoController::class,                            'update'])->name('sgc.gestao.update');
  Route::post('dav/storeDavProfissionais',                    [StoreDavProfissionais::class,                           'index'])->name('sgc.gestao.storeDavProfissionais');

});
