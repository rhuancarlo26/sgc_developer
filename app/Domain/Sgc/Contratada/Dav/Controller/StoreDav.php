<?php

namespace App\Domain\Sgc\Contratada\Dav\Controller;

use App\Domain\Sgc\Contratada\Dav\Services\DavService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreDav extends Controller
{
  public function __construct(private readonly DavService $davService)
  {
  }

  public function index(Request $request)
  {

    try {
      $dados = $request->validate([
        'contrato'       => 'required|string',
        'empreendimento' => 'required|string',
        'coordenador'    => 'required|string',
        'finalidade'     => 'required|string',
        'escopo'         => 'required|string',
        'dataInicio'     => 'required|date',
        'dataFinal'      => 'required|date',
        'produto'        => 'required|string',
        'subproduto'     => 'required|string',
        'origem'         => 'required|array',
        'origem.*'       => 'string',
        'destino'        => 'required|array',
        'destino.*'      => 'string',
        'profissionais'  => 'array',
        'transporte'     => 'array',
        'status'         => 'required|string'
      ]);

      $this->davService->salvarDav($dados);

      // Retorna apenas a mensagem de sucesso sem os dados completos
      return back()->with('success', 'Registro cadastrado com sucesso!');
    
    }catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => 'Erro ao cadastrar!', 'error' => $e->getMessage()], 500);
    }
  }
}