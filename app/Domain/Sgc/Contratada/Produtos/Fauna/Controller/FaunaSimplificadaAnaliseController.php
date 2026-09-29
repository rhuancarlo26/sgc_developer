<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\SgcFaunaEntregaSimplificadaService;
use App\Models\SgcEntregaSimplificada;
use App\Models\SgcFaunaCampanha;
use App\Models\SgcModulo;
use App\Models\SgcvwEmpreendimentos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FaunaSimplificadaAnaliseController extends Controller
{
    public function __construct(private readonly SgcFaunaEntregaSimplificadaService $service)
    {
    }

    public function show(int $contrato, string $produto, int $campanha)
    {
        abort_unless($produto === 'fauna', 404);

        [$campanhaFauna, $entrega] = $this->buscarEntrega($contrato, $campanha);

        return Inertia::render('Sgc/Contratada/Produtos/Fauna/Simplificada/VisualizarSimplificada', [
            'campanha' => $this->campanhaParaTela($campanhaFauna, $entrega),
            'campanha_id' => $campanha,
            'contrato' => $contrato,
            'produto' => 'fauna',
            'contratos' => $campanhaFauna->contrato,
            'canApprove' => Auth::user()?->perfis_id === 3 && $campanhaFauna->status === 'Em análise',
        ]);
    }

    public function analise(int $contrato, string $produto, int $campanha)
    {
        abort_unless($produto === 'fauna', 404);
        abort_unless(Auth::user()?->perfis_id === 3, 403, 'Apenas fiscais podem analisar campanhas.');

        [$campanhaFauna, $entrega] = $this->buscarEntrega($contrato, $campanha);
        abort_unless($campanhaFauna->status === 'Em análise', 422, 'Campanha não está em análise.');

        return Inertia::render('Sgc/Contratada/Produtos/Fauna/Simplificada/AnaliseSimplificada', [
            'campanha' => $this->campanhaParaTela($campanhaFauna, $entrega),
            'contrato' => $contrato,
            'produto' => 'fauna',
            'contratos' => $campanhaFauna->contrato,
            'canApprove' => true,
            'analises' => $entrega->analises()
                ->with('fiscal:id,name')
                ->latest('created_at')
                ->get()
                ->map(fn ($analise) => [
                    'id' => $analise->id,
                    'versao_analise' => $analise->versao_analise,
                    'status' => $analise->status,
                    'observacoes' => $analise->observacoes,
                    'fiscal' => $analise->fiscal,
                    'created_at' => optional($analise->created_at)->format('d/m/Y H:i'),
                ]),
        ]);
    }

    public function edit(int $contrato, string $produto, int $campanha)
    {
        abort_unless($produto === 'fauna', 404);
        abort_if(Auth::user()?->perfis_id === 3, 403, 'Fiscais não podem editar campanhas.');

        [$campanhaFauna, $entrega] = $this->buscarEntrega($contrato, $campanha);
        abort_unless(in_array($campanhaFauna->status, ['Em elaboração', 'Rejeitada']), 422, 'Esta campanha não pode ser editada.');

        return Inertia::render('Sgc/Contratada/Produtos/Fauna/Simplificada/CreateSimplificada', [
            'campanha' => $this->campanhaParaTela($campanhaFauna, $entrega),
            'contrato' => $contrato,
            'produto' => 'Fauna',
            'contratos' => $campanhaFauna->contrato,
            'modulos' => SgcModulo::query()->select(['id', 'nome', 'nome_planilha_modelo'])->get(),
            'empreendimentos' => SgcvwEmpreendimentos::where('contrato_id', $contrato)->pluck('cod_emp')->toArray(),
            'subproduto' => $campanhaFauna->subproduto,
        ]);
    }

    public function update(Request $request, int $contrato, string $produto, int $campanha)
    {
        abort_unless($produto === 'fauna', 404);
        abort_if(Auth::user()?->perfis_id === 3, 403, 'Fiscais não podem editar campanhas.');

        $dados = $request->validate((new \App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\StoreEntregaSimplificadaFaunaRequest())->rules());
        [$campanhaFauna, $entrega] = $this->buscarEntrega($contrato, $campanha);
        abort_unless(in_array($campanhaFauna->status, ['Em elaboração', 'Rejeitada']), 422, 'Esta campanha não pode ser editada.');

        $this->service->atualizar($campanhaFauna, $entrega, $dados);

        return redirect()
            ->route('sgc.contratada.produtos.fauna.simplificada.show', [$contrato, $produto, $campanha])
            ->with('success', 'Campanha simplificada atualizada com sucesso.');
    }

    public function aprovar(int $contrato, string $produto, int $campanha)
    {
        return $this->salvarResultado($contrato, $produto, $campanha, 'Aprovada', null);
    }

    public function reprovar(Request $request, int $contrato, string $produto, int $campanha)
    {
        $dados = $request->validate(['observacoes' => 'required|string|min:10']);

        return $this->salvarResultado($contrato, $produto, $campanha, 'Rejeitada', $dados['observacoes']);
    }

    private function salvarResultado(int $contrato, string $produto, int $campanha, string $status, ?string $observacoes)
    {
        abort_unless($produto === 'fauna', 404);
        abort_unless(Auth::user()?->perfis_id === 3, 403, 'Apenas fiscais podem analisar campanhas.');

        [, $entrega] = $this->buscarEntrega($contrato, $campanha);
        $this->service->registrarAnalise($entrega, $status, $observacoes, Auth::id());

        return redirect()
            ->route('sgc.contratada.produtos.index', [$contrato, $produto])
            ->with('success', "Campanha {$status} com sucesso.");
    }

    private function buscarEntrega(int $contrato, int $campanha): array
    {
        $campanhaFauna = SgcFaunaCampanha::where('id_contrato', $contrato)
            ->where('modo_preenchimento', 'simplificado')
            ->findOrFail($campanha);

        $entrega = SgcEntregaSimplificada::where('id_contrato', $contrato)
            ->where('produto_tipo', 'fauna')
            ->where('entidade_tipo', 'fauna_campanha')
            ->where('entidade_id', $campanhaFauna->id)
            ->with(['planilhasFauna.modulo', 'fotos', 'anexos', 'analises.fiscal'])
            ->latest()
            ->firstOrFail();

        return [$campanhaFauna, $entrega];
    }

    private function campanhaParaTela(SgcFaunaCampanha $campanha, SgcEntregaSimplificada $entrega): array
    {
        return [
            'id' => $campanha->id,
            'id_campanha' => $campanha->id_campanha,
            'cod_emp' => $campanha->cod_emp,
            'sei_dnit' => $campanha->sei_dnit,
            'subproduto' => $campanha->subproduto,
            'status' => $campanha->status,
            'versao_analise' => $entrega->versao_analise,
            'planilhas' => $entrega->planilhasFauna
                ->mapWithKeys(fn ($planilha) => [$planilha->tipo => [
                    'id' => $planilha->id,
                    'tipo' => $planilha->tipo,
                    'modulo_id' => $planilha->modulo_id,
                    'modulo' => $planilha->modulo?->only(['id', 'nome', 'nome_planilha_modelo']),
                    'nome_arquivo' => $planilha->nome_arquivo,
                    'caminho_arquivo' => $planilha->caminho_arquivo,
                    'url' => $planilha->caminho_arquivo ? Storage::url($planilha->caminho_arquivo) : null,
                    'mime_type' => $planilha->mime_type,
                    'tamanho_bytes' => $planilha->tamanho_bytes,
                ]])
                ->all(),
            'fotos' => $entrega->fotos->map(fn ($foto) => [
                'id' => $foto->id,
                'nome_arquivo' => $foto->nome_arquivo,
                'url' => $foto->caminho_arquivo ? Storage::url($foto->caminho_arquivo) : null,
                'latitude' => $foto->latitude,
                'longitude' => $foto->longitude,
                'data_captura' => optional($foto->data_captura)->format('d/m/Y H:i'),
                'descricao' => $foto->descricao,
            ])->values(),
            'anexos' => $entrega->anexos->map(fn ($anexo) => [
                'id' => $anexo->id,
                'nome_arquivo' => $anexo->nome_arquivo,
                'url' => $anexo->caminho_arquivo ? Storage::url($anexo->caminho_arquivo) : null,
            ])->values(),
            'analises' => $entrega->analises->map(fn ($analise) => [
                'id' => $analise->id,
                'versao' => $analise->versao_analise,
                'status' => $analise->status,
                'observacoes' => $analise->observacoes,
                'fiscal' => $analise->fiscal?->only(['id', 'name']),
                'created_at' => optional($analise->created_at)->format('d/m/Y H:i'),
            ])->values(),
        ];
    }
}
