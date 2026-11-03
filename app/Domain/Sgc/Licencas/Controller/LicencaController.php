<?php

namespace App\Domain\Sgc\Licencas\Controller;

use App\Domain\Sgc\Licencas\Enums\SituacaoCondicionante;
use App\Domain\Sgc\Licencas\Enums\TipoPrazo;
use App\Domain\Sgc\Licencas\Enums\TipoRegistro;
use App\Domain\Sgc\Licencas\Requests\LicencaRequest;
use App\Domain\Sgc\Licencas\Services\LicencaService;
use App\Domain\Sgc\Licencas\Services\PainelService;
use App\Models\Contrato;
use App\Models\SgcLicenca;
use App\Models\SgcLicencaPrazo;
use App\Models\SgcLicencaTipo;
use App\Models\Uf;
use App\Models\User;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LicencaController extends Controller
{
    public function __construct(
        private readonly LicencaService $licencaService,
        private readonly PainelService $painelService
    ) {
    }

    public function index(Request $request): Response
    {
        $filtros = $request->only(['busca', 'tipo_id', 'uf_id', 'status', 'regularizacao']);

        return Inertia::render('Sgc/Licencas/Index', [
            'licencas' => $this->licencaService->listar($filtros),
            'filtros' => $filtros,
            'tipos' => SgcLicencaTipo::orderBy('sigla')->get(['id', 'sigla', 'nome']),
            'ufs' => Uf::orderBy('uf')->get(['id', 'uf']),
        ]);
    }

    public function create(Request $request): Response
    {
        $anterior = $request->integer('anterior') ? SgcLicenca::findOrFail($request->integer('anterior')) : null;
        $motivo = in_array($request->input('motivo'), ['retificacao', 'renovacao']) ? $request->input('motivo') : 'retificacao';

        $licenca = $anterior
            ? [
                ...$anterior->only(['tipo_id', 'orgao_emissor', 'uf_id', 'empreendimento', 'processo_sei', 'antecedencia_renovacao_dias', 'contrato_id']),
                'data_validade' => $motivo === 'retificacao' ? $anterior->data_validade?->format('Y-m-d') : null,
                'licenca_anterior_id' => $anterior->id,
                'motivo_vinculo' => $motivo,
                'copiar_condicionantes' => true,
                'responsaveis' => $anterior->responsaveis()->pluck('users.id'),
            ]
            : null;

        return Inertia::render('Sgc/Licencas/Form', [
            ...$this->opcoesFormulario(),
            'licenca' => $licenca,
            'anterior' => $anterior ? ['id' => $anterior->id, 'rotulo' => $anterior->load('tipo')->rotulo()] : null,
            'tipoInicial' => $request->boolean('regularizacao') ? SgcLicencaTipo::where('regularizacao', true)->value('id') : null,
            'ufInicial' => $request->integer('uf_id') ?: null,
        ]);
    }

    public function store(LicencaRequest $request): RedirectResponse
    {
        $licenca = $this->licencaService->salvar(null, $request->validated(), $request->file('arquivo'), Auth::id());

        return to_route('sgc.licencas.show', $licenca)
            ->with('message', ['type' => 'success', 'content' => 'Licença cadastrada. Cadastre as condicionantes para gerar os prazos.']);
    }

    public function show(SgcLicenca $licenca): Response
    {
        $licenca->load([
            'tipo', 'uf:id,uf,nome', 'contrato:id,numero_contrato', 'responsaveis:id,name,email',
            'anterior.tipo:id,sigla', 'sucessoras.tipo:id,sigla', 'prazoRenovacao',
            'condicionantes' => fn ($q) => $q->withCount(['prazos as prazos_pendentes_count' => fn ($p) => $p->where('status', 'pendente')]),
        ]);

        $prazos = $licenca->prazos()
            ->with(['condicionante:id,numero', 'registros.user:id,name'])
            ->orderBy('data_limite')
            ->get()
            ->map(fn (SgcLicencaPrazo $p) => [
                ...$this->painelService->resumoPrazo($p),
                'registros' => $p->registros->map(fn ($r) => [
                    ...$r->only(['id', 'tipo', 'numero_oficio', 'numero_sei', 'descricao', 'arquivo_nome']),
                    'data' => $r->data?->format('Y-m-d'),
                    'data_limite_anterior' => $r->data_limite_anterior?->format('Y-m-d'),
                    'data_limite_nova' => $r->data_limite_nova?->format('Y-m-d'),
                    'usuario' => $r->user?->name,
                    'criado_em' => $r->created_at?->format('d/m/Y H:i'),
                ]),
            ]);

        return Inertia::render('Sgc/Licencas/Show', [
            'licenca' => [
                ...$licenca->toArray(),
                ...$this->licencaService->resumo($licenca),
                'rotulo' => $licenca->rotulo(),
                'anterior' => $licenca->anterior ? ['id' => $licenca->anterior->id, 'rotulo' => $licenca->anterior->rotulo()] : null,
                'sucessoras' => $licenca->sucessoras->map(fn ($s) => ['id' => $s->id, 'rotulo' => $s->rotulo(), 'motivo' => $s->motivo_vinculo]),
            ],
            'prazos' => $prazos,
            'tiposPrazo' => TipoPrazo::options(),
            'situacoesCondicionante' => SituacaoCondicionante::options(),
            'tiposRegistro' => TipoRegistro::options(),
        ]);
    }

    public function edit(SgcLicenca $licenca): Response
    {
        return Inertia::render('Sgc/Licencas/Form', [
            ...$this->opcoesFormulario(),
            'licenca' => [
                ...$licenca->toArray(),
                'data_emissao' => $licenca->data_emissao?->format('Y-m-d'),
                'data_validade' => $licenca->data_validade?->format('Y-m-d'),
                'responsaveis' => $licenca->responsaveis()->pluck('users.id'),
            ],
            'anterior' => null,
            'tipoInicial' => null,
            'ufInicial' => null,
        ]);
    }

    public function update(LicencaRequest $request, SgcLicenca $licenca): RedirectResponse
    {
        $this->licencaService->salvar($licenca, $request->validated(), $request->file('arquivo'), Auth::id());

        return to_route('sgc.licencas.show', $licenca)
            ->with('message', ['type' => 'success', 'content' => 'Licença atualizada e prazos recalculados.']);
    }

    public function destroy(SgcLicenca $licenca): RedirectResponse
    {
        $this->licencaService->excluir($licenca);

        return to_route('sgc.licencas.index')->with('message', ['type' => 'success', 'content' => 'Licença excluída.']);
    }

    public function arquivar(SgcLicenca $licenca): RedirectResponse
    {
        $this->licencaService->alternarArquivamento($licenca);

        $mensagem = $licenca->status === 'arquivada' ? 'Licença arquivada.' : 'Licença reativada.';

        return back()->with('message', ['type' => 'success', 'content' => $mensagem]);
    }

    public function arquivo(SgcLicenca $licenca): StreamedResponse
    {
        abort_unless($licenca->arquivo_caminho, 404);

        return Storage::disk(config('sgc_licencas.disco'))->download($licenca->arquivo_caminho, $licenca->arquivo_nome);
    }

    private function opcoesFormulario(): array
    {
        return [
            'tipos' => SgcLicencaTipo::where('ativo', true)->orderByDesc('regularizacao')->orderBy('sigla')->get(['id', 'sigla', 'nome', 'regularizacao']),
            'ufs' => Uf::orderBy('uf')->get(['id', 'uf', 'nome']),
            'usuarios' => User::where('ativo', 1)->orderBy('name')->get(['id', 'name', 'email']),
            'contratos' => Contrato::orderBy('numero_contrato')->get(['id', 'numero_contrato']),
            'orgaos' => SgcLicenca::distinct()->orderBy('orgao_emissor')->pluck('orgao_emissor')
                ->merge(['Ibama', 'ICMBio', 'IPHAN', 'FUNAI', 'Fundação Cultural Palmares'])->unique()->values(),
            'antecedenciaPadrao' => config('sgc_licencas.antecedencia_renovacao_padrao'),
        ];
    }
}
