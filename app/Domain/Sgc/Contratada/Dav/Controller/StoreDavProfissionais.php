<?php

namespace App\Domain\Sgc\Contratada\Dav\Controller;

use App\Domain\Sgc\Contratada\Dav\Services\DavService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreDavProfissionais extends Controller
{
    public function __construct(
        private readonly DavService $davService
    ) {}

    public function index(Request $request)
    {
        try {
            $dados = $request->validate([
                'profissionais' => 'required|array',
                'profissionais.*.nome' => 'required|string',
                'profissionais.*.formacao' => 'required|string',
                'profissionais.*.contrato_id' => 'required|int',
            ]);

            $profissionalCriado = null;

            foreach ($dados['profissionais'] as $profissional) {

                if ($this->davService->profissionalExiste(
                    $profissional['nome'],
                    $profissional['contrato_id']
                )) {
                    return response()->json([
                        'success' => false,
                        'message' => "O profissional {$profissional['nome']} já está cadastrado."
                    ], 422);
                }
                $profissionalCriado = $this->davService->salvarDavProfissionais($profissional);
            }

            return response()->json([
                'success' => true,
                'profissional' => $profissionalCriado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cadastrar profissional',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
