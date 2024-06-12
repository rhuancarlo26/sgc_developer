<?php

namespace App\Domain\Sgc\Contratada\app\Controller;

use App\Domain\Sgc\Contratada\app\Services\RecursoService;
use App\Models\Contrato;
use App\Models\SgcComentario;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class RecursoSgcController extends Controller
{
  public function __construct(private readonly RecursoService $recursoService)
  {
  }

  public function index(Contrato $contrato): Response
  {
    $comentarios = SgcComentario::all();

    return Inertia::render('Sgc/Contratada/Recurso/Index', [
        'contrato' => $contrato,
        'comentarios' => $comentarios
    ]);
  }


}
