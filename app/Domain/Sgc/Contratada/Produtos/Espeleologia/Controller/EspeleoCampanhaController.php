<?php

namespace App\Domain\Sgc\Contratada\Produtos\Espeleologia\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Services\EspeleoService;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Request\EspeleoSalvarCampanhaRequest;
use App\Models\SgcEspeleoProfissional;
use App\Models\SgcvwEmpreendimentos;
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
        $profissionais = SgcEspeleoProfissional::where('id_contrato', $contrato)->get()->map(function ($prof) {
            return [
                'id' => $prof->id,
                'profissional' => $prof->profissional,
                'formacao' => $prof->formacao,
            ];
        });

        return Inertia::render('Sgc/Contratada/Produtos/Espeleologia/Create', [
            'contrato' => $contrato,
            'produto' => ucfirst($produto),
            'subproduto' => $request->subproduto,
            'empreendimentos' => SgcvwEmpreendimentos::where('contrato_id', $contrato)->get()->map(function ($emp) {
                return [
                    'cod_emp' => $emp->cod_emp,
                    'subtrecho' => $emp->subtrecho_ini && $emp->subtrecho_fin ? $emp->subtrecho_ini . ' - ' . $emp->subtrecho_fin : '',
                    'segmento' => $emp->km_ini && $emp->km_fin ? $emp->km_ini . ' - ' . $emp->km_fin : '',
                    'extensao' => $emp->km_fin && $emp->km_ini ? $emp->km_fin - $emp->km_ini : 0,
                    'tipo_de_intervencao' => $emp->tipo_de_intervencao ?? '',
                    'descricao' => $emp->descricao ?? '',
                    'bioma' => $emp->bioma ?? '',
                ];
            }),
            'campanhaId' => $request->session()->get('campanhaId', null),
            'draftData' => $request->session()->get('draftData', []),
            'profissionais' => $profissionais,
            'success' => 'Profissional cadastrado com sucesso',
        ])->with(['flash' => ['success' => 'Profissional cadastrado com sucesso', 'profissional' => $profissional]]);
    }

    public function getProfissionais(Request $request, $contrato, $produto)
    {
        $profissionais = SgcEspeleoProfissional::where('id_contrato', $contrato)->get();
        return response()->json(['profissionais' => $profissionais]);
    }

}



    