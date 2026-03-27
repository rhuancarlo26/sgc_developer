<?php

namespace App\Domain\Sgc\Contratada\Produtos\Espeleologia\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Services\EspeleoService;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Services\EspeleoAnexoService;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Request\EspeleoSalvarCampanhaRequest;
use App\Models\SgcEspeleoProfissional;
use App\Models\SgcvwEmpreendimentos;
use App\Models\SgcEspeleoCampanha;
use App\Models\SgcEspeleoResultadoAnexo;
use App\Models\SgcEspeleoEstudosPosteriores;
use App\Models\Contrato;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EspeleoCampanhaController extends Controller
{

    protected $anexoService;


    protected $espeleoService;

    public function __construct(EspeleoService $espeleoService, EspeleoAnexoService $anexoService)
    {
        $this->espeleoService = $espeleoService;
        $this->anexoService = $anexoService;
    }

    public function show($contrato, $produto, $campanhaId)
    {
        if ($produto !== 'espeleologia') {
            abort(404, 'Produto inválido');
        }

        $campanha = SgcEspeleoCampanha::with([
            'justificativas',
            'metodologia',
            'resultadoAnexos',
            'anexos',
            'profissionais',
        ])->findOrFail($campanhaId);

        if ((int) $campanha->id_contrato !== (int) $contrato) {
            abort(403, 'Acesso negado');
        }

        $empreendimento = SgcvwEmpreendimentos::where('cod_emp', $campanha->cod_emp)->first();
        $coordenadas = $empreendimento->coordenadas ?? null;

        $contratoObj = Contrato::find($campanha->id_contrato);

        $campanhaData = [
            'id' => $campanha->id,
            'id_campanha' => $campanha->id_campanha,
            'cod_emp' => $campanha->cod_emp,
            'subproduto' => $campanha->subproduto,
            'subtrecho' => $campanha->subtrecho,
            'segmento' => $campanha->segmento,
            'extensao' => $campanha->extensao,
            'tipo_de_intervencao' => $campanha->tipo_de_intervencao,
            'descricao' => $campanha->descricao,
            'bioma' => $campanha->bioma,
            'status' => $campanha->status,
            'metodologia' => optional($campanha->metodologia)->metodologia,
            'justificativas' => $campanha->justificativas->map(function ($justificativa) {
                return [
                    'id' => $justificativa->id,
                    'tipo' => $justificativa->tipo,
                    'titulo' => $justificativa->titulo,
                    'justificativa' => $justificativa->justificativa,
                    'codigo_sei' => $justificativa->codigo_sei,
                ];
            })->values(),
            'resultados_anexos' => $campanha->resultadoAnexos->map(function ($anexo) {
                return [
                    'id' => $anexo->id,
                    'nome_arquivo' => $anexo->nome_arquivo,
                    'tipo' => $anexo->tipo,
                    'comentario' => $anexo->comentario,
                    'caminho' => \App\Helpers\StorageHelper::publicUrl($anexo->caminho),
                    'created_at' => optional($anexo->created_at)->format('d/m/Y H:i'),
                ];
            })->values(),
            'anexos' => $campanha->anexos->map(function ($anexo) {
                return [
                    'id' => $anexo->id,
                    'tipo_anexo' => $anexo->tipo,
                    'nome_arquivo' => $anexo->nome,
                    'legenda' => $anexo->legenda,
                    'caminho' => \App\Helpers\StorageHelper::publicUrl($anexo->caminho),
                    'created_at' => optional($anexo->created_at)->format('d/m/Y H:i'),
                ];
            })->values(),
            'profissionais' => $campanha->profissionais->map(function ($profissional) {
                return [
                    'id' => $profissional->id,
                    'profissional' => $profissional->profissional,
                    'formacao' => $profissional->formacao,
                    'funcao' => $profissional->funcao,
                ];
            })->values(),
        ];

        return Inertia::render('Sgc/Contratada/Produtos/Espeleologia/VisualizarCampanha', [
            'campanha' => $campanhaData,
            'campanha_id' => $campanha->id,
            'contrato' => $campanha->id_contrato,
            'produto' => 'Espeleologia',
            'contratos' => $contratoObj,
            'canApprove' => Auth::user()->perfis_id === 2 && $campanha->status === 'Em análise',
            'coordenadas' => $coordenadas,
        ]);
    }

    public function approve(Request $request, $contrato, $produto, $campanhaId)
    {
        if ($produto !== 'espeleologia') {
            abort(404, 'Produto inválido');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:Aprovada,Rejeitada',
            'observacoes' => 'nullable|string|max:5000',
        ]);

        $campanha = SgcEspeleoCampanha::findOrFail($campanhaId);
        if ((int) $campanha->id_contrato !== (int) $contrato) {
            abort(403, 'Acesso negado');
        }

        $campanha->status = $validated['status'];
        $campanha->save();

        return back()->with('success', 'Campanha atualizada com sucesso.');
    }

    public function salvarCampanha(EspeleoSalvarCampanhaRequest $request, $contrato, $produto)
    {
        if ($produto !== 'espeleologia') {
            Log::error('Produto inválido', ['produto' => $produto]);
            abort(404, 'Produto inválido');
        }

        $data = $request->validated();
        Log::info('Dados validados para salvar campanha', [
            'data' => $data,
            'contrato' => $contrato,
            'campanhaId' => $request->id,
            'codigo_sei' => $data['justificativas'][0]['codigo_sei'] ?? 'Não fornecido',
            'justificativas_count' => count($data['justificativas'] ?? []),
        ]);

        try {
            $campanha = $this->espeleoService->salvarCampanha($data, $contrato, $request->id);
            Log::info('Campanha salva com sucesso', ['campanha_id' => $campanha->id]);
            return Inertia::location(route('sgc.contratada.produtos.index', [$contrato, $produto]));
        } catch (\Exception $e) {
            Log::error('Erro ao salvar campanha', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
            ]);
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
                    'coordenadas' => $emp->coordenadas ? json_decode($emp->coordenadas, true) : null,
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

    public function uploadPlanilhaFeicoes(Request $request, $contrato, $produto)
    {
        if ($produto !== 'espeleologia') abort(404, 'Produto inválido');

        $validated = $request->validate([
            'file'        => 'required|file|mimes:xlsx,xls,csv|max:20480',
            'campanha_id' => 'required|exists:sgc_espeleo_campanhas,id',
            'comentario'  => 'nullable|string|max:5000',
        ]);

        $campanha = SgcEspeleoCampanha::findOrFail($validated['campanha_id']);
        if ((int) $campanha->id_contrato !== (int) $contrato) abort(403, 'Acesso negado');

        $file        = $request->file('file');
        $nomeOriginal = $file->getClientOriginalName();
        $ext          = $file->getClientOriginalExtension();
        $nomeUnico    = 'espeleo_' . $contrato . '_campanha_' . $campanha->id_campanha . '_feicoes_' . time() . '.' . $ext;

        $caminhoPasta    = 'planilhas/espeleologia/' . $contrato . '/' . $campanha->id_campanha;
        $caminhoCompleto = $caminhoPasta . '/' . $nomeUnico;

        // Remove planilha anterior de feições da mesma campanha
        $anterior = SgcEspeleoResultadoAnexo::where('campanha_id', $campanha->id)
            ->where('tipo', 'feicoes')
            ->first();
        if ($anterior) {
            Storage::disk('public')->delete($anterior->caminho);
            $anterior->delete();
        }

        $file->storeAs($caminhoPasta, $nomeUnico, 'public');

        $anexo = SgcEspeleoResultadoAnexo::create([
            'campanha_id'  => $campanha->id,
            'id_contrato'  => $contrato,
            'nome_arquivo' => $nomeOriginal,
            'caminho'      => $caminhoCompleto,
            'tipo'         => 'feicoes',
            'comentario'   => $validated['comentario'] ?? null,
        ]);

        $resultadosAnexos = SgcEspeleoResultadoAnexo::where('id_contrato', $contrato)
            ->where('campanha_id', $campanha->id)
            ->get()
            ->map(function ($a) {
                return [
                    'id'           => $a->id,
                    'nome_arquivo' => $a->nome_arquivo,
                    'caminho'      => $a->caminho,
                    'tipo'         => $a->tipo,
                    'comentario'   => $a->comentario,
                    'url_publica'  => \App\Helpers\StorageHelper::publicUrl($a->caminho),
                ];
            });

        return response()->json([
            'success'          => true,
            'message'          => 'Planilha de feições cársticas vinculada com sucesso.',
            'anexo'            => [
                'id'           => $anexo->id,
                'nome_arquivo' => $anexo->nome_arquivo,
                'caminho'      => $caminhoCompleto,
                'tipo'         => 'feicoes',
                'comentario'   => $anexo->comentario,
                'url_publica'  => \App\Helpers\StorageHelper::publicUrl($caminhoCompleto),
            ],
            'resultadosAnexos' => $resultadosAnexos,
        ]);
    }

    public function uploadResultadoAnexo(Request $request, $contrato, $produto)
    {
        if ($produto !== 'espeleologia') abort(404, 'Produto inválido');

        $tiposPermitidos = [
            'geologico',
            'geomorfologico',
            'cavidades',
            'hidrografico',
            'hipsometrico',
            'declividades',
            'limites_areas',
            'potencial_inicial',
            'potencial_reclassificado',
            'projeto_engenharia',
            'estudos_posteriores',
            // Prospecção
            'feicoes',
            'feicoes_carsticas_identificadas',
            'cavidades_nao_encontradas',
            'cavidades_cecav_canie',
            'caminhamento',
            'raio_de_250m_de_cavidades',
            'curvas_de_nivel',
        ];

        $validated = $request->validate([
            'campanha_id' => 'required|exists:sgc_espeleo_campanhas,id',
            'tipo' => 'required|in:' . implode(',', $tiposPermitidos),
            'comentario' => 'nullable|string|max:5000',
        ]);

        // "feicoes" é planilha comum; os demais continuam como ZIP de shapefile.
        if ($validated['tipo'] === 'feicoes') {
            $request->validate([
                'zip_file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
            ]);
        } else {
            $request->validate([
                'zip_file' => 'required|file|mimes:zip|max:10240',
            ]);
        }

        $campanha = SgcEspeleoCampanha::findOrFail($validated['campanha_id']);
        if ($campanha->id_contrato != $contrato) abort(403, 'Acesso negado');

        $zipFile = $request->file('zip_file');
        $nomeOriginal = $zipFile->getClientOriginalName();
        $ext = $zipFile->getClientOriginalExtension();
        $isPlanilhaFeicoes = $validated['tipo'] === 'feicoes';
        $nomeUnico = 'espeleo_' . $contrato . '_campanha_' . $campanha->id_campanha . '_' . time() . '.' . $ext;

        $caminhoPasta = $isPlanilhaFeicoes
            ? 'planilhas/espeleologia/' . $contrato . '/' . $campanha->id_campanha
            : 'shapefiles/espeleologia/' . $contrato . '/' . $campanha->id_campanha;
        $caminhoCompleto = $caminhoPasta . '/' . $nomeUnico;

        $zipFile->storeAs($caminhoPasta, $nomeUnico, 'public');

        $anexo = SgcEspeleoResultadoAnexo::create([
            'campanha_id' => $campanha->id,
            'id_contrato' => $contrato,
            'nome_arquivo' => $nomeOriginal,
            'caminho' => $caminhoCompleto,
            'tipo' => $validated['tipo'],
            'comentario' => $validated['comentario'] ?? null,
        ]);

        // Busca todos os anexos atualizados
        $resultadosAnexos = SgcEspeleoResultadoAnexo::where('id_contrato', $contrato)
            ->where('campanha_id', $campanha->id)
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'nome_arquivo' => $a->nome_arquivo,
                    'caminho' => $a->caminho,
                    'tipo' => $a->tipo,
                    'comentario' => $a->comentario,
                    'url_publica' => \App\Helpers\StorageHelper::publicUrl($a->caminho),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Arquivo vinculado com sucesso.',
            'anexo' => [
                'id' => $anexo->id,
                'nome_arquivo' => $anexo->nome_arquivo,
                'caminho' => $anexo->caminho,
                'tipo' => $anexo->tipo,
                'comentario' => $anexo->comentario,
                'url_publica' => \App\Helpers\StorageHelper::publicUrl($anexo->caminho),
            ],
            'resultadosAnexos' => $resultadosAnexos,
        ]);
    }

    public function updateResultadoAnexo(Request $request, $contrato, $produto, $id)
    {
        if ($produto !== 'espeleologia') abort(404, 'Produto inválido');

        $validated = $request->validate([
            'comentario' => 'nullable|string|max:5000',
        ]);

        $anexo = SgcEspeleoResultadoAnexo::findOrFail($id);
        if ($anexo->id_contrato != $contrato) abort(403, 'Acesso negado');

        $anexo->comentario = $validated['comentario'] ?? '';
        $anexo->save();

        // Nada de JSON. Redireciona (303) para satisfazer o Inertia.
        return back(303);
        // Se quiser flash: return back(303)->with('flash', ['success' => 'Observação salva.']);
    }

    public function deleteResultadoAnexo(Request $request, $contrato, $produto, $id)
    {
        if ($produto !== 'espeleologia') {
            abort(404, 'Produto inválido');
        }

        $anexo = SgcEspeleoResultadoAnexo::findOrFail($id);
        if ((int) $anexo->id_contrato !== (int) $contrato) {
            abort(403, 'Acesso negado');
        }

        if ($anexo->caminho && Storage::disk('public')->exists($anexo->caminho)) {
            Storage::disk('public')->delete($anexo->caminho);
        }

        $anexo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Anexo de resultado excluido com sucesso.',
        ]);
    }



    public function uploadAnexo(Request $request, $contrato, $produto)
    {
        try {
            $anexos = $this->anexoService->uploadAnexo($request, $contrato, $produto);
            return response()->json([
                'success' => true,
                'message' => 'Imagens vinculadas com sucesso.',
                'anexos' => $anexos,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao vincular imagens', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao vincular imagens: ' . $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    public function updateAnexo(Request $request, $contrato, $produto, $id)
    {
        try {
            $this->anexoService->updateAnexo($request, $contrato, $produto, $id);
            return response()->json([
                'success' => true,
                'message' => 'Legenda atualizada com sucesso.'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar anexo', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar anexo: ' . $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    public function deleteAnexo(Request $request, $contrato, $produto, $id)
    {
        try {
            $this->anexoService->deleteAnexo($contrato, $produto, $id);
            return response()->json([
                'success' => true,
                'message' => 'Imagem excluída com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir anexo: ' . $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }


    public function storeEstudosPosteriores(Request $request, $contrato, $produto)
    {
        if ($produto !== 'espeleologia') abort(404, 'Produto inválido');

        $validated = $request->validate([
            'campanha_id' => 'required|exists:sgc_espeleo_campanhas,id',
            'necessario' => 'required|boolean',
            'estudos' => 'array',
            'estudos.*.subproduto_id' => 'required_if:necessario,1|integer',
            'estudos.*.quantidade' => 'nullable|string|max:50',
            'estudos.*.coordenadas' => 'array',
            'estudos.*.coordenadas.*.lat' => 'nullable|string|max:50',
            'estudos.*.coordenadas.*.lng' => 'nullable|string|max:50',
        ]);

        $campanha = SgcEspeleoCampanha::findOrFail($validated['campanha_id']);
        if ($campanha->id_contrato != $contrato) abort(403, 'Acesso negado');

        // ✅ Apagar todos registros anteriores
        SgcEspeleoEstudosPosteriores::where('campanha_id', $campanha->id)->delete();

        if ($validated['necessario']) {
            foreach ($validated['estudos'] as $e) {
                SgcEspeleoEstudosPosteriores::create([
                    'id_contrato' => $contrato,
                    'campanha_id' => $campanha->id,
                    'subproduto_id' => $e['subproduto_id'],
                    'quantidade' => $e['quantidade'] ?? null,
                    'coordenadas' => json_encode($e['coordenadas'] ?? []),
                    'necessario' => true,
                ]);
            }
        }

        return back()->with('success', 'Estudos posteriores atualizados.');
    }



}
