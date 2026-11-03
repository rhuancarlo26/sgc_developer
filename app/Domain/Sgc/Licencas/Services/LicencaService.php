<?php

namespace App\Domain\Sgc\Licencas\Services;

use App\Models\SgcLicenca;
use App\Models\SgcLicencaCondicionante;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LicencaService
{
    public function __construct(private readonly PrazoService $prazoService)
    {
    }

    public function listar(array $filtros): LengthAwarePaginator
    {
        $status = $filtros['status'] ?? 'ativa';

        return SgcLicenca::query()
            ->with(['tipo:id,sigla,nome,regularizacao', 'uf:id,uf', 'prazoRenovacao'])
            ->withCount(['prazos as prazos_vencidos_count' => fn ($q) => $q
                ->where('status', 'pendente')->whereDate('data_limite', '<', today())])
            ->when($status !== 'todas', fn ($q) => $q->where('status', $status))
            ->when(isset($filtros['regularizacao']) && $filtros['regularizacao'] !== '', fn ($q) => $q->regularizacao((bool) $filtros['regularizacao']))
            ->when($filtros['tipo_id'] ?? null, fn ($q, $tipo) => $q->where('tipo_id', $tipo))
            ->when($filtros['uf_id'] ?? null, fn ($q, $uf) => $q->where('uf_id', $uf))
            ->when($filtros['busca'] ?? null, fn ($q, $busca) => $q->where(fn ($w) => $w
                ->where('numero', 'like', "%{$busca}%")
                ->orWhere('orgao_emissor', 'like', "%{$busca}%")
                ->orWhere('empreendimento', 'like', "%{$busca}%")
                ->orWhere('processo_sei', 'like', "%{$busca}%")))
            ->orderByRaw('data_validade is null, data_validade')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (SgcLicenca $licenca) => $this->resumo($licenca));
    }

    /**
     * Dados da licença com a situação calculada, para listagens.
     */
    public function resumo(SgcLicenca $licenca): array
    {
        $situacao = $licenca->situacao();

        return [
            ...$licenca->only(['id', 'numero', 'orgao_emissor', 'empreendimento', 'processo_sei', 'status', 'arquivo_nome']),
            'data_emissao' => $licenca->data_emissao?->format('Y-m-d'),
            'data_validade' => $licenca->data_validade?->format('Y-m-d'),
            'tipo' => $licenca->tipo?->only(['id', 'sigla', 'nome', 'regularizacao']),
            'uf' => $licenca->uf?->uf,
            'prazo_renovacao' => $licenca->prazoRenovacao?->data_limite?->format('Y-m-d'),
            'prazos_vencidos_count' => $licenca->prazos_vencidos_count ?? null,
            'situacao' => ['valor' => $situacao->value, 'label' => $situacao->label(), 'cor' => $situacao->cor()],
        ];
    }

    public function salvar(?SgcLicenca $licenca, array $dados, ?UploadedFile $arquivo, ?int $userId): SgcLicenca
    {
        return DB::transaction(function () use ($licenca, $dados, $arquivo, $userId) {
            $nova = $licenca === null;
            $licenca ??= new SgcLicenca(['status' => 'ativa', 'created_by' => $userId]);

            $campos = collect($dados)->only($licenca->getFillable())
                ->except(['status', 'created_by', 'arquivo_nome', 'arquivo_caminho'])
                ->all();

            // O vínculo com a licença anterior só é definido na criação
            if (!$nova) {
                unset($campos['licenca_anterior_id'], $campos['motivo_vinculo']);
            }

            $licenca->fill($campos);

            if ($arquivo) {
                $this->removerArquivo($licenca);
                $licenca->arquivo_nome = $arquivo->getClientOriginalName();
                $licenca->arquivo_caminho = $arquivo->store('sgc_licencas/' . ($licenca->id ?? 'novas'), config('sgc_licencas.disco'));
            }

            $licenca->save();
            $licenca->responsaveis()->sync($dados['responsaveis'] ?? []);

            if ($nova && $licenca->licenca_anterior_id) {
                $this->sucederAnterior($licenca, (bool) ($dados['copiar_condicionantes'] ?? false));
            }

            $this->prazoService->sincronizar($licenca);

            if ($nova && $licenca->licenca_anterior_id) {
                $this->prazoService->transferirDaAnterior($licenca);
            }

            return $licenca;
        });
    }

    /**
     * Ativa ↔ arquivada. Licenças substituídas não mudam de status por aqui.
     */
    public function alternarArquivamento(SgcLicenca $licenca): void
    {
        if ($licenca->status === 'substituida') {
            return;
        }

        $licenca->update(['status' => $licenca->status === 'arquivada' ? 'ativa' : 'arquivada']);
        $this->prazoService->sincronizar($licenca);
    }

    public function excluir(SgcLicenca $licenca): void
    {
        DB::transaction(function () use ($licenca) {
            // Uma anterior substituída volta a ficar ativa se a sucessora for excluída
            if ($licenca->licenca_anterior_id) {
                SgcLicenca::whereKey($licenca->licenca_anterior_id)->where('status', 'substituida')->update(['status' => 'ativa']);
            }

            $licenca->delete();
        });
    }

    /**
     * Retificação/renovação: a anterior passa a "substituída" e, se pedido, suas condicionantes são copiadas.
     */
    private function sucederAnterior(SgcLicenca $licenca, bool $copiarCondicionantes): void
    {
        $anterior = SgcLicenca::with('condicionantes')->findOrFail($licenca->licenca_anterior_id);

        if ($copiarCondicionantes) {
            $anterior->condicionantes->each(fn (SgcLicencaCondicionante $c) => $licenca->condicionantes()->create(
                collect($c->getAttributes())->only($c->getFillable())->except('licenca_id')->all()
            ));
        }

        $anterior->update(['status' => 'substituida']);
    }

    private function removerArquivo(SgcLicenca $licenca): void
    {
        if ($licenca->arquivo_caminho) {
            Storage::disk(config('sgc_licencas.disco'))->delete($licenca->arquivo_caminho);
        }
    }
}
