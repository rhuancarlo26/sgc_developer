<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Services;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Imports\PMQAPontoImport;
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
    public function indexParaCampanha(SgcPmqaCampanha $campanha, array $searchParams = []): array
    {
        $columns = $searchParams['columns'] ?? null;
        $value = $searchParams['value'] ?? null;

        $query = $this->searchAllColumns($columns, $value)
            ->where('campanha_id', $campanha->id)
            ->orderBy('chave');

        $paginator = $query->paginate(15)->appends($searchParams);

        return ['pontos' => $paginator];
    }

    /**
     * Importa um arquivo Excel/CSV para a campanha especificada.
     */
    public function importarParaCampanha(SgcPmqaCampanha $campanha, UploadedFile $arquivo): array
    {
        $pmqaPontoImport = new PMQAPontoImport();

        try {
            $collection = Excel::toCollection($pmqaPontoImport, $arquivo);
            $rows = $collection->first() ?? collect();
        } catch (\Throwable $e) {
            Log::error('Erro lendo arquivo de pontos PMQA: ' . $e->getMessage());
            return ['type' => 'error', 'content' => 'Arquivo inválido ou erro ao ler arquivo.'];
        }

        $created = 0;
        $updated = 0;

        foreach ($rows as $row) {
            $rowArr = collect($row)->mapWithKeys(function ($v, $k) {
                $key = preg_replace('/\s+/', '_', trim((string)$k));
                $key = mb_strtolower($key);
                return [$key => $v];
            })->toArray();

            $mapped = [
                'campanha_id' => $campanha->id,
                'chave' => $rowArr['chave'] ?? $rowArr['codigo'] ?? $rowArr['codigo_ponto'] ?? null,
                'nome_ponto_coleta' => $rowArr['nome_ponto_coleta'] ?? $rowArr['nomepontocoleta'] ?? $rowArr['nome'] ?? null,
                'zona' => $rowArr['zona'] ?? null,
                'lat_x' => $rowArr['lat_x'] ?? $rowArr['latitude'] ?? $rowArr['lat'] ?? null,
                'long_y' => $rowArr['long_y'] ?? $rowArr['longitude'] ?? $rowArr['lon'] ?? $rowArr['long'] ?? null,
                'classificacao' => $rowArr['classificacao'] ?? null,
                'classe' => $rowArr['classe'] ?? null,
                'tipo_ambiente' => $rowArr['tipo_ambiente'] ?? null,
                'UF' => $rowArr['uf'] ?? ($rowArr['estado'] ?? null),
                'municipio' => $rowArr['municipio'] ?? null,
                'bacia_hidrografica' => $rowArr['bacia_hidrografica'] ?? ($rowArr['bacia'] ?? null),
                'km_rodovia' => $rowArr['km_rodovia'] ?? $rowArr['km'] ?? null,
                'estaca' => $rowArr['estaca'] ?? null,
            ];

            if (empty($mapped['chave'])) {
                Log::warning('Linha ignorada na importação de pontos PMQA por falta de chave', ['row' => $rowArr]);
                continue;
            }

            try {
                // usa Eloquent updateOrCreate direto para evitar depender de dataManagement.updateOrCreate
                $modelClass = $this->modelClass;
                $p = $modelClass::updateOrCreate(
                    ['campanha_id' => $campanha->id, 'chave' => $mapped['chave']],
                    $mapped
                );

                // verificar se foi criado (no Eloquent não há wasRecentlyCreated em retorno estático, então inferimos)
                // -> wasRecentlyCreated só existe na instância se foi criado via save(); aqui usamos atributo
                // para saber criado/atualizado consultamos se created_at == updated_at (aprox)
                if ($p->wasRecentlyCreated ?? false) {
                    $created++;
                } else {
                    // Se não temos wasRecentlyCreated, uma aproximação razoável:
                    // se created_at == updated_at -> criado agora (quando updateOrCreate cria, created_at == updated_at)
                    if ($p->created_at && $p->updated_at && $p->created_at->eq($p->updated_at)) {
                        $created++;
                    } else {
                        $updated++;
                    }
                }
            } catch (\Throwable $e) {
                Log::error("Erro ao inserir/atualizar ponto PMQA [{$mapped['chave']}]: {$e->getMessage()}");
            }
        }

        return ['type' => 'success', 'content' => "Importação concluída. Criados: {$created}, Atualizados: {$updated}"];
    }

    /**
     * Atualiza (ou cria) ponto garantindo campanha_id.
     */
    public function updateParaCampanha(array $updateRequest): array
    {
        if (empty($updateRequest['campanha_id'])) {
            return ['type' => 'error', 'content' => 'campanha_id é obrigatório.'];
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
