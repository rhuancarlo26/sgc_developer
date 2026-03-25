<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Services;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Imports\PMQAPontoImport;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaPonto;
use App\Models\SgcPmqaCampanha;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PontoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = SgcPmqaPonto::class;

    /**
     * Lista pontos filtrados por campanha e parâmetros de busca.
     */
    public function indexParaCampanha(SgcPmqa $pmqa, array $searchParams = []): array
    {
        $columns = $searchParams['columns'] ?? null;
        $value = $searchParams['value'] ?? null;

        $query = $this->searchAllColumns($columns, $value)
            ->where('pmqa_id', $pmqa->id)
            ->orderBy('id');

        $paginator = $query->paginate(15)->appends($searchParams);

        return ['pontos' => $paginator];
    }

    /**
     * Importa um arquivo Excel/CSV para a campanha especificada.
     */
    public function importarParaCampanha(SgcPmqa $pmqa, UploadedFile $arquivo): array
    {
        Log::info('Iniciando importação', ['pmqa_id' => $pmqa->id, 'arquivo' => $arquivo->getClientOriginalName()]);

        try {
            $collection = Excel::toCollection(new PMQAPontoImport(), $arquivo);
            $rows = $collection->first() ?? collect();
            Log::info('Linhas lidas', ['total' => $rows->count()]);
        } catch (\Throwable $e) {
            Log::error('Erro lendo arquivo: ' . $e->getMessage());
            return ['type' => 'error', 'content' => 'Arquivo inválido ou erro ao ler.'];
        }

        $created = 0;
        $updated = 0;

        foreach ($rows as $index => $row) {
            $rowArr = collect($row)->mapWithKeys(function ($v, $k) {
                $key = preg_replace('/\s+/', '_', trim((string) $k));
                $key = mb_strtolower($key);  // normaliza para minúsculo
                return [$key => $v];
            })->toArray();

            Log::info('Linha processada', ['index' => $index + 2, 'dados' => $rowArr]);

            $mapped = [
                'pmqa_id' => $pmqa->id,
                'nome_ponto_coleta' => $rowArr['nome_ponto_coleta'] ?? $rowArr['nomepontocoleta'] ?? $rowArr['nome'] ?? null,
                'zona' => $rowArr['zona'] ?? null,
                'lat_x' => $rowArr['lat_x'] ?? $rowArr['latitude'] ?? $rowArr['lat'] ?? null,
                'long_y' => $rowArr['long_y'] ?? $rowArr['longitude'] ?? $rowArr['lon'] ?? $rowArr['long'] ?? null,
                'classificacao' => $rowArr['classificacao'] ?? null,
                'classe' => $rowArr['classe'] ?? null,
                'tipo_ambiente' => $rowArr['tipo_ambiente'] ?? null,
                'uf' => $rowArr['uf'] ?? $rowArr['estado'] ?? null,
                'municipio' => $rowArr['municipio'] ?? null,
                'bacia_hidrografica' => $rowArr['bacia_hidrografica'] ?? $rowArr['bacia'] ?? null,
                'km_rodovia' => $rowArr['km_rodovia'] ?? $rowArr['km'] ?? null,
                'estaca' => $rowArr['estaca'] ?? null,
                'observacoes' => $rowArr['observacoes'] ?? $rowArr['observacao'] ?? null,
            ];

            // Sem verificação de 'chave', processa todas as linhas

            try {
                $p = SgcPmqaPonto::updateOrCreate(
                    [
                        'pmqa_id' => $pmqa->id,
                        'nome_ponto_coleta' => $mapped['nome_ponto_coleta'],  // critério de unicidade (mude se preferir outro campo)
                    ],
                    $mapped
                );

                Log::info('Ponto salvo', ['id' => $p->id, 'criado' => $p->wasRecentlyCreated]);

                if ($p->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            } catch (\Throwable $e) {
                Log::error('Erro ao salvar ponto: ' . $e->getMessage(), ['dados' => $mapped]);
            }
        }

        return ['type' => 'success', 'content' => "Importação concluída. Criados: {$created}, Atualizados: {$updated}"];
    }

    /**
     * Atualiza (ou cria) ponto garantindo campanha_id.
     */
    public function updateParaCampanha(array $updateRequest): array
    {
        // dd($updateRequest);
        if (empty($updateRequest['id'])) {
            return ['type' => 'error', 'content' => 'id é obrigatório.'];
        }

        $modelClass = $this->modelClass;

        try {
            if (!empty($updateRequest['id'])) {
                $ponto = $modelClass::find($updateRequest['id']);
                if (!$ponto) {
                    return ['type' => 'error', 'content' => 'Ponto não encontrado.'];
                }
                $ponto->update($updateRequest);
                return ['type' => 'success', 'content' => 'Ponto atualizado', 'id' => $ponto->id];
            }

            $ponto = $modelClass::create($updateRequest);
            return ['type' => 'success', 'content' => 'Ponto criado', 'id' => $ponto->id ?? null];
        } catch (\Throwable $e) {
            Log::error('Erro em updateParaCampanha: ' . $e->getMessage(), ['data' => $updateRequest]);
            return ['type' => 'error', 'content' => 'Erro ao salvar ponto.'];
        }
    }

    /**
     * Deleta ponto (mantive assinatura).
     */
    public function deletePonto(SgcPmqaPonto $ponto): array
    {
        try {
            $ponto->delete();
            return ['type' => 'success', 'content' => 'Ponto removido'];
        } catch (\Throwable $e) {
            Log::error('Erro ao deletar ponto PMQA: ' . $e->getMessage(), ['id' => $ponto->id]);
            return ['type' => 'error', 'content' => 'Erro ao deletar ponto'];
        }
    }
}
