<?php

namespace App\Domain\Sgc\Contratada\Dav\Controller;

use App\Domain\Sgc\Contratada\Dav\Services\DavService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreDavProfissionais extends Controller
{
  public function __construct(private readonly DavService $davService)
  {
  }

  public function index(Request $request)
  {

    try {
        $dados = $request->validate([
            'profissionais' => 'required|array',
            'profissionais.*.nome' => 'required|string',
            'profissionais.*.formacao' => 'required|string',
            'profissionais.*.contrato_id' => 'required|int',
        ]);

        // Aqui você pode iterar sobre os profissionais
        foreach ($dados['profissionais'] as $profissional) {
            $this->davService->salvarDavProfissionais($profissional);
        }

      // Retorna apenas a mensagem de sucesso sem os dados completos
      return back()->with('success', 'Registro cadastrado com sucesso!');
    
    }catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => 'Erro ao cadastrar!', 'error' => $e->getMessage()], 500);
    }
  }
}