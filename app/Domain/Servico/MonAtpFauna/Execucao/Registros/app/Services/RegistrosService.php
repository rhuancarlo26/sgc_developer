<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Services;

use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\imports\RegistroImport;
use App\Models\AtFaunaExecucaoRegistro;
use App\Models\AtFaunaExecucaoRegistroImagem;
use App\Models\Servicos;
use App\Models\Uf;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use App\Shared\Utils\ArquivoUtils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\AtFaunaGrupoAmostradoModel;

class RegistrosService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = AtFaunaExecucaoRegistro::class;

    public function index(Servicos $servico, array $searchParams, bool $paginate = true): LengthAwarePaginator|Builder
    {
        return $this->searchAllColumns(...$searchParams)
            ->select([
                'at_fauna_execucao_registro.*',
                'base_rodovia.rodovia',
                'estados.nome AS nome_estado',
                DB::raw('DATE_FORMAT(at_fauna_execucao_registro.data_registro, "%d/%m/%Y") as data_registroF'),
                'fga.nome AS nome_grupo_amostrado',
                'aferi.chave as chave_imagem'
            ])
            ->join('at_fauna_execucao_campanhas AS fec', 'fec.id', '=', 'at_fauna_execucao_registro.fk_campanha')
            ->leftJoin('at_fauna_execucao_registro_imagem AS aferi', 'at_fauna_execucao_registro.id', '=', 'aferi.id_registro')
            ->join('servico_licenca_condicionante AS slc', 'slc.id', '=', 'fec.fk_servico_licenca')
            ->join('licencas', 'licencas.id', '=', 'slc.id_licenca')
            ->leftJoin('licencas_br', 'licencas_br.licenca_id', '=', 'licencas.id')
            ->leftJoin('base_rodovia', 'base_rodovia.id', '=', 'licencas_br.rodovia')
            ->leftJoin('estados', 'estados.id', '=', 'at_fauna_execucao_registro.fk_estado')
            ->join('at_fauna_grupo_amostrado AS fga', 'fga.id', '=', 'at_fauna_execucao_registro.fk_grupo_amostrado')
            ->where('at_fauna_execucao_registro.fk_servico', $servico->id)
            ->when($paginate, fn($q) => $q->paginate());
    }

    public function store(array $request): array
    {
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

        if ($request['arquivo']) {
            $this->saveImage($request['arquivo'], $response['model']['id']);
        }

        return $response;
    }

    public function update(array $request): array
    {
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

        if ($request['arquivo']) {
            $this->saveImage($request['arquivo'], $request['id']);
        }

        return $response;
    }

    private function saveImage(UploadedFile $file, int $idRegistro): void
    {
        $arquivo = (new ArquivoUtils())->salvar(
            arquivo: $file,
            diretorio: 'atropelamento-fauna/arquivo/registro/fotografico',
            createModel: false,
        );
        AtFaunaExecucaoRegistroImagem::create([
            'chave' => $arquivo['chave'],
            'caminho_imagem' => $arquivo['diretorio'],
            'nome' => $arquivo['nome_arquivo'],
            'id_registro' => $idRegistro
        ]);
    }
   
    public function store_importar(array $post)
    {
        $response = [
            'model' => null,
            'request' => [
                'type' => 'success',
                'content' => 'Importação concluída com sucesso!',
                'error' => ''
            ]
        ];

        
        $passagemFaunaImport = new RegistroImport();
        $registros = Excel::toCollection($passagemFaunaImport, $post['arquivo'])->first();

        $erros = [];

      
        $map = [
            'Grupo Amostrado'                => 'fk_grupo_amostrado',
            'Data do Registro'               => 'data_registro',
            'Hora do Registro'               => 'hora_registro',
            'UF'                             => 'uf_final',
            'Km'                             => 'km',
            'Latitude'                       => 'latitude',
            'Longitude'                      => 'longitude',
            'Sentido'                        => 'sentido',
            'Margem'                         => 'margem',
            'Pavimentado'                    => 'pavimentado',
            'Classe'                         => 'classe',
            'Ordem'                          => 'ordem',
            'Família'                        => 'familia',
            'Gênero'                         => 'genero',
            'Espécie'                        => 'especie',
            'Nome Comum'                     => 'nome_comum',
            'Sexo'                           => 'sexo',
            'Faixa Etária'                   => 'faixa_etaria',
            'Coletado'                       => 'coletado',
            'Num de Tombamento'              => 'n_registro_tombamento',
            'Carcaca Removida'               => 'carcaca_removida',
            'Redução Biológica'              => 'reducao_biologica',
            'Qnt de Indivíduos'              => 'n_individuos',
            'Status Conservação Estadual'    => 'estadual',
            'Status Conservação Federal'     => 'federal',
            'Status Conservação IUCN'        => 'iucn',
        ];

        
        $normalize = function (?string $s): string {
            if ($s === null) return '';
            $s = trim($s);      
            $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
            $s = strtolower($s); 
            $s = preg_replace('/\s+/', ' ', $s);
            return $s ?? '';
        };

        $gruposMap = AtFaunaGrupoAmostradoModel::select('id', 'nome')
            ->get()
            ->mapWithKeys(function ($g) use ($normalize) {
                return [$normalize($g->nome) => (int)$g->id];
            })
            ->all();

        foreach ($registros as $i => $registroOld) {
            try {
              
                $registro = [];
                foreach ($map as $from => $to) {
                    $registro[$to] = $registroOld[$from] ?? null;
                }

              
                $nomeGrupo = $registro['fk_grupo_amostrado'] ?? null;
                $grupoId = $gruposMap[$normalize($nomeGrupo)] ?? null;

                if (!$grupoId) {
                    throw new \Exception("Grupo Amostrado '{$nomeGrupo}' não encontrado na base (linha " . ($i + 2) . ").");
                }
                $registro['fk_grupo_amostrado'] = $grupoId;

                
                if (!isset($registro['data_registro']) || $this->getDateYMD($registro['data_registro']) === null) {
                    throw new \Exception("Campo 'data_registro' ausente ou inválido (linha " . ($i + 2) . ").");
                }

              
                $uf = Uf::where('nome', $registro['uf_final'])
                    ->orWhere('uf', $registro['uf_final'])
                    ->first();

               
                $response = $this->dataManagement->create(entity: $this->modelClass, infos: [
                    ...$registro,
                    'fk_estado'     => $uf->id ?? null,
                    'fk_campanha'   => $post['campanha_id'],
                    'fk_servico'    => $post['servico_id'],
                    'sentido'       => strtoupper($registro['sentido'][0] ?? ''),
                    'margem'        => strtoupper($registro['margem'][0] ?? ''),
                    'data_registro' => $this->getDateYMD($registro['data_registro'] ?? null),
                    'hora_registro' => $this->getHoraHM($registro['hora_registro'] ?? null),
                ]);
            } catch (\Throwable $e) {
                $erros[] = "Linha " . ($i + 2) . ": " . $e->getMessage();
                Log::error("Erro na linha " . ($i + 2) . " da importação de registros de fauna: ", [
                    'registro' => $registro ?? [],
                    'erro' => $e->getMessage()
                ]);
            }
        }

        if (count($erros)) {
            return [
                'model' => null,
                'request' => [
                    'type' => 'error',
                    'content' => 'Falha ao importar os seguintes registros:',
                    'error' => implode("\n", $erros)
                ]
            ];
        }

        return $response;
    }


    private function getDateYMD(string|int|float|null $date): ?string
    {
        if (is_null($date)) return null;

        try {
            if (is_string($date)) {
                if (str_contains($date, '/')) {
                    return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
                }

                if (str_contains($date, '-')) {
                    return Carbon::parse($date)->format('Y-m-d');
                }
            }

            if (is_numeric($date)) {
                return Date::excelToDateTimeObject($date)->format('Y-m-d');
            }

            if ($date instanceof Carbon) {
                return $date->format('Y-m-d');
            }
        } catch (\Throwable $e) {
            Log::error("Erro ao converter data: " . json_encode($date) . ' => ' . $e->getMessage());
        }

        return null;
    }


    private function getHoraHM(string|float|int|null $hora): ?string
    {
        if (is_null($hora)) return null;

        if (is_string($hora) && str_contains($hora, ':')) {
            try {
                return Carbon::createFromFormat('H:i:s', $hora)->format('H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        }

        if (is_numeric($hora)) {
            try {
                return Date::excelToDateTimeObject($hora)->format('H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }


    public function graficos_monitora_atp(Servicos $servico): array
    {
        $allRegistros = AtFaunaExecucaoRegistro::with(['grupo_faunistico', 'campanhas'])->where('fk_servico', $servico->id)
            ->get();

        $especiesGroup = $allRegistros->filter(function ($registro) {
            return !empty($registro->especie);
        })->groupBy('especie');

        $sortedEspeciesGroup = $especiesGroup->sortByDesc(function ($grupo) {
            return $grupo->count();
        });

        return [
            'especiesGroup' => $sortedEspeciesGroup,
            'chartDataPieAbundancia'  => $this->getChartDataPieAbundancia($allRegistros),
            'chartDataPieDiversidade' => $this->getChartDataPieDiversidade($allRegistros),
            'getChartDataBarCampanhas'            => $this->getChartDataBarCampanhas($allRegistros),
            'chartDataBar2'           => $this->getChartDataBar2($especiesGroup)
            //   'modulos' => ServicoMonitoraFaunaConfigModuloAmostral::with('armadilhas')->where('fk_servico', $servico->id)->get(['id', 'tamanho_modulo']),
        ];
    }

    private function getChartDataBarCampanhas($allRegistros): array
    {
        $groupCampanhas = $allRegistros->groupBy(function ($registro) {
            return $registro->campanhas
                ? $registro->campanhas->id
                : 'Sem Campanha';
        });

        return [
            'labels'   => $groupCampanhas->keys()->toArray(),
            'datasets' => [
                [
                    'label'           => 'Ocorrências',
                    'data'            => $groupCampanhas->map(function ($grupo) {
                        return $grupo->count();
                    })->values()->toArray(),
                    'backgroundColor' => "#007bff",
                    'borderRadius'    => 5,
                ],
            ],
        ];
    }


    private function getChartDataPieAbundancia($allRegistros): array
    {
        $abundancia = $allRegistros->groupBy(function ($registro) {

            return $registro->grupo_faunistico
                ? $registro->grupo_faunistico->nome
                : 'Sem Grupo';
        })->map(function ($grupoRegistros, $grupoNome) {
            return [
                'grupo_faunistico' => $grupoNome,
                'total' => $grupoRegistros->count(),
            ];
        })->values();

        return [
            'labels' => $abundancia->pluck('grupo_faunistico')->toArray(),
            'datasets' => [
                [
                    'data' => $abundancia->pluck('total')->toArray(),
                    'backgroundColor' => [
                        "#E07A5F",
                        "#92A8D1",
                        "#F2E394",
                        "#6CA0DC",
                        "#F7CAC9",
                        "#9D85C1"
                    ],
                    'borderColor' => "#ffffff",
                    'borderWidth' => 2,
                ],
            ],
        ];
    }

    private function getChartDataPieDiversidade($allRegistros): array
    {
        $diversidade = $allRegistros->groupBy(function ($registro) {
            return $registro->grupo_faunistico
                ? $registro->grupo_faunistico->nome
                : 'Sem Grupo';
        })->map(function ($grupoRegistros, $grupoNome) {
            $uniqueSpecies = $grupoRegistros->pluck('especie')->unique();
            return [
                'grupo_faunistico' => $grupoNome,
                'total' => $uniqueSpecies->count(),
            ];
        })->values();

        return [
            'labels' => $diversidade->pluck('grupo_faunistico')->toArray(),
            'datasets' => [
                [
                    'data' => $diversidade->pluck('total')->toArray(),
                    'backgroundColor' => [
                        "#E07A5F",
                        "#92A8D1",
                        "#F2E394",
                        "#6CA0DC",
                        "#F7CAC9",
                        "#9D85C1"
                    ],
                    'borderColor' => "#ffffff",
                    'borderWidth' => 2,
                ],
            ],
        ];
    }

    private function getChartDataBar2($especiesGroup): array
    {
        // Ordena os grupos de acordo com a contagem de ocorrências (do maior para o menor)
        $sortedGroup = $especiesGroup->sortByDesc(function ($grupo) {
            return $grupo->count();
        });

        return [
            'labels' => $sortedGroup->keys()->toArray(),
            'datasets' => [
                [
                    'label' => 'Ocorrências',
                    'data' => $sortedGroup->map(function ($grupo) {
                        return $grupo->count();
                    })->values()->toArray(),
                    'backgroundColor' => "rgba(30, 144, 255, 0.8)",
                    'borderColor' => "rgba(30, 144, 255, 1)",
                    'borderWidth' => 1,
                ],
            ],
        ];
    }
}
