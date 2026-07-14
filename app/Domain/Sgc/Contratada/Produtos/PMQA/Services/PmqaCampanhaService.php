<?php

namespace App\Domain\Sgc\Contratada\Produtos\Pmqa\Services;

use App\Domain\Sgc\Contratada\Produtos\Pmqa\Imports\CampanhaPontosImport;
use App\Models\SgcPmqaCampanha;
use App\Models\SgcPmqaPonto;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PmqaCampanhaService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = SgcPmqaPonto::class;


    public function importarPontos(SgcPmqaCampanha $campanha, UploadedFile $arquivo): array
    {
        $import = new CampanhaPontosImport();

        $pontosCollection = Excel::toCollection($import, $arquivo)->first();

        $pontosImportados = 0;
        $erros = [];

        DB::transaction(function () use ($campanha, $pontosCollection, &$pontosImportados, &$erros) {
            foreach ($pontosCollection as $row) {
                try {
                    // Usamos o SgcPmqaPonto::create que você já tinha, pois ele está correto.
                    SgcPmqaPonto::create([
                        'campanha_id' => $campanha->id,
                        'nome_ponto_coleta' => $row['nome_ponto_coleta'],
                        'lat_x' => $row['lat_x'],
                        'long_y' => $row['long_y'],
                        'classificacao' => $row['classificacao'],
                        'classe' => $row['classe'],
                        'tipo_ambiente' => $row['tipo_ambiente'],
                        'UF' => $row['uf'],
                        'municipio' => $row['municipio'],
                        'bacia_hidrografica' => $row['bacia_hidrografica'],
                        'km_rodovia' => $row['km_rodovia'],
                        'estaca' => $row['estaca'],
                        'observacoes' => $row['observacoes'],
                    ]);
                    $pontosImportados++;
                } catch (\Throwable $e) {
                    $mensagemErro = "Erro ao inserir o ponto '{$row['nome_ponto_coleta']}'. Motivo: {$e->getMessage()}";
                    $erros[] = $mensagemErro;
                }
            }
            if ($pontosImportados > 0) {
                $campanha->update(['fase' => 'pontos_importados']);
            }
        });
        return [
            'success' => count($erros) === 0,
            'message' => "Importação concluída. Pontos importados: {$pontosImportados}. Erros: " . count($erros) . ".",
            'pontos_count' => $pontosImportados,
            'errors' => $erros,
        ];
    }

    public function index(SgcPmqaCampanha $campanha, array $searchParams): array
    {
        return [
            'pontos' => $this->searchAllColumns(...$searchParams)
                ->where('campanha_id', $campanha->id)
                ->with(['campanha']) // Relacionamento com a campanha, se definido
                ->paginate()
                ->appends($searchParams),
        ];
    }


    public function destroy(SgcPmqaPonto $ponto)
    {
        $response = $this->dataManagement->delete($this->modelClass, $ponto->id);

        return $response;
    }
}
