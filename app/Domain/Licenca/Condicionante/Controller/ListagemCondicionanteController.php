<?php

namespace App\Domain\Licenca\Condicionante\Controller;

use App\Domain\Licenca\Condicionante\Services\CondicionanteService;
use App\Models\Licenca;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListagemCondicionanteController extends Controller
{
  public function __construct(private readonly CondicionanteService $condicionanteService)
  {
  }

  public function index(Licenca $licenca, Request $request): Response
  {
    $searchParams = $request->all('searchColumn', 'searchValue');

    $condicionantes = $this->condicionanteService->listarCondicionantes($licenca, $searchParams);

    return Inertia::render('Licenca/Condicionante/Index', [
      'licenca' => $licenca,
      'condicionantes' => $condicionantes
    ]);
  }
}