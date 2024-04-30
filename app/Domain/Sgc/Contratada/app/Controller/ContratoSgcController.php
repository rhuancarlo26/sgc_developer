<?php

namespace App\Domain\Sgc\Contratada\app\Controller;

use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ContratoSgcController extends Controller
{
  public function index(Contrato $contrato): Response
  {
    return Inertia::render('Sgc/Contratada/Index', [
      'contrato' => $contrato
    ]);
  }
}
