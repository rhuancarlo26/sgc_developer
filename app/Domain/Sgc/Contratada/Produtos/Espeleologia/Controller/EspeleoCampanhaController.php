<?php

namespace App\Domain\Sgc\Contratada\Produtos\Espeleologia\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Services\EspeleoService;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Request\EspeleoSalvarCampanhaRequest;
use App\Models\SgcEspeleoProfissional;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EspeleoCampanhaController extends Controller
{
    protected $espeleoService;

    public function __construct(EspeleoService $espeleoService)
    {
        $this->espeleoService = $espeleoService;
    }

    public function salvarCampanha(EspeleoSalvarCampanhaRequest $request, $contrato, $produto)
    {
        if ($produto !== 'espeleologia') {
            Log::error('Produto inválido', ['produto' => $produto]);
            abort(404, 'Produto inválido');
        }

        $data = $request->validated();
        Log::info('Dados recebidos para salvar campanha', ['data' => $data, 'contrato' => $contrato, 'campanhaId' => $request->id]);

        try {
            $campanha = $this->espeleoService->salvarCampanha($data, $contrato, $request->id);
            Log::info('Campanha salva com sucesso', ['campanha_id' => $campanha->id]);
            return Inertia::location(route('sgc.contratada.produtos.index', [$contrato, $produto]));
        } catch (\Exception $e) {
            Log::error('Erro ao salvar campanha', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    public function storeProfissional(Request $request, $contrato, $produto)
    {
        $validated = $request->validate([
            'id_contrato' => 'required|exists:contratos,id',
            'profissional' => 'required|string|max:255',
            'formacao' => 'nullable|string|max:255',
            'telefone' => 'nullable|string',
            'cpf' => 'nullable|string',
            'email' => 'nullable|email',
            'curriculum_lattes' => 'nullable|string',
            'funcao' => 'nullable|string|max:255',
            'ctf' => 'nullable|string',
            'validade' => 'nullable|date',
            'conselho_de_classe' => 'nullable|string',
            'numero_de_registro' => 'nullable|integer',
            'status' => 'nullable|string',
            'observacao' => 'nullable|string',
            'subproduto' => 'nullable|string',
        ]);

        $profissional = SgcEspeleoProfissional::create($validated);
        return response()->json(['message' => 'Profissional cadastrado com sucesso', 'profissional' => $profissional]);
    }

    public function getProfissionais(Request $request, $contrato, $produto)
    {
        $profissionais = SgcEspeleoProfissional::where('id_contrato', $contrato)->get();
        return response()->json(['profissionais' => $profissionais]);
    }

}