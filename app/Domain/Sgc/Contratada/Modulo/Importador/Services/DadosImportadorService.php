<?php

namespace App\Domain\Modulos\Importador\Services;

use App\Domain\Modulos\Importador\Jobs\ProcessarPlanilhaImportadorJob;
use App\Models\ModuloImportador;
use App\Models\ModuloImportadorDados;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class DadosImportadorService extends Controller
{
    public function buscarDados(ModuloImportador $importador, Request $request): array
    {
        $page = max((int) $request->get('page', 1), 1);
        // $perPage = max((int) $request->get('per_page', 10), 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $columnsSql = collect($importador->modulo?->campos ?? [])
            ->map(function ($campo) {
                $nomeCampo = str_replace('`', '', $campo['nome_campo']);
                $tipoCampo = $campo['tipo'] ?? 'texto';

                $sqlType = match ($tipoCampo) {
                    'inteiro' => 'INT',
                    'decimal' => 'DECIMAL(15,4)',
                    'data' => 'VARCHAR(50)',
                    default => 'VARCHAR(255)',
                };

                return "`{$nomeCampo}` {$sqlType} PATH '$.\"{$nomeCampo}\"'";
            });

        if (!$columnsSql->count())
            return [
                'current_page' => $page,
                'data' => [],
                'per_page' => $perPage,
                'total' => 0,
                'last_page' => 1,
            ];

        $columnsSqlImp = $columnsSql->implode(",\n");

        $rows = DB::select("
            SELECT jt.*
            FROM modulo_importador_dados mid
            CROSS JOIN JSON_TABLE(
                mid.dados,
                '$' COLUMNS (
                    {$columnsSqlImp}
                )
            ) AS jt
            WHERE mid.modulo_importador_id = ?
            LIMIT {$perPage} OFFSET {$offset}
        ", [$importador->id]);


        $total = $importador->dadosJson?->count();
        $lastPage = max((int) ceil($total / $perPage), 1);

        $from = $total > 0 ? $offset + 1 : null;
        $to = $total > 0 ? min($offset + $perPage, $total) : null;

        $baseUrl = $request->url();
        $query = $request->query();

        $buildPageUrl = function (int $targetPage) use ($baseUrl, $query, $perPage) {
            return $baseUrl . '?' . http_build_query([
                ...$query,
                'page' => $targetPage,
                // 'per_page' => $perPage,
            ]);
        };

        $links = [
            [
                'url' => $page > 1 ? $buildPageUrl($page - 1) : null,
                'label' => '&laquo; Anterior',
                'active' => false,
            ],
        ];

        for ($i = 1; $i <= $lastPage; $i++) {
            $links[] = [
                'url' => $buildPageUrl($i),
                'label' => (string) $i,
                'active' => $i === $page,
            ];
        }

        $links[] = [
            'url' => $page < $lastPage ? $buildPageUrl($page + 1) : null,
            'label' => 'Próximo &raquo;',
            'active' => false,
        ];

        return [
            'current_page' => $page,
            'data' => $rows,
            'from' => $from,
            'last_page' => $lastPage,
            'links' => $links,
            'path' => $baseUrl,
            'per_page' => $perPage,
            'to' => $to,
            'total' => $total,
            'first_page_url' => $buildPageUrl(1),
            'last_page_url' => $buildPageUrl($lastPage),
            'next_page_url' => $page < $lastPage ? $buildPageUrl($page + 1) : null,
            'prev_page_url' => $page > 1 ? $buildPageUrl($page - 1) : null,
        ];
    }

    public function importarPlanilha(ModuloImportador $importador, UploadedFile $arquivo): void
    {
        if ($importador->dadosJson()->exists()) {
            throw new \RuntimeException('Já existem dados importados para esta planilha.');
        }

        $nomeArquivo = $arquivo->getClientOriginalName();

        $caminhoArquivo = $arquivo->storeAs(
            'Importador' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nomeArquivo
        );

        $importador->update([
            'nome_arquivo' => $nomeArquivo,
            'load' => false,
            'desc_erros' => null,
        ]);

        $job = new ProcessarPlanilhaImportadorJob(
            importadorId: $importador->id,
            caminhoArquivo: $caminhoArquivo,
            extensaoArquivo: $arquivo->getClientOriginalExtension()
        );

        dispatch_sync($job);
    }

    public function excluirDados(ModuloImportador $importador): int
    {
        return ModuloImportadorDados::where('modulo_importador_id', $importador->id)->delete();
    }
}
