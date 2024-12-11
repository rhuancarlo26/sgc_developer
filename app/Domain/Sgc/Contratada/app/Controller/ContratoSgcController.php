<?php

namespace App\Domain\Sgc\Contratada\app\Controller;

use App\Models\Contrato;
use App\Models\DashexcelEmpreendimentos;
use App\Models\SgcvwEstudos;
use App\Models\SgcvwSubprodutos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ContratoSgcController extends Controller
{
  public function index(Contrato $contrato): Response
  {
    $empreendimentos = DashexcelEmpreendimentos::all();
    $estudos = SgcvwEstudos::all();
    $subprodutos = SgcvwSubprodutos::all();

    return Inertia::render('Sgc/Contratada/Index', [
      'contrato' => $contrato,
      'empreendimentos' => $empreendimentos,
      'estudos' => $estudos,
      'subprodutos' => $subprodutos
    ]);
  }
  

}
