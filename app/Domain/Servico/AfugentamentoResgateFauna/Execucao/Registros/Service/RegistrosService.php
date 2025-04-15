<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Service;

use App\Models\AfugentFaunaExecFrenteModel;
use App\Models\AfugentFaunaExecRegistroImagemModel;
use App\Models\AfugentFaunaExecRegistroModel;
use App\Models\AfugentFaunaFormaRegistroModel;
use App\Models\AtFaunaGrupoAmostradoModel;
use App\Models\Uf;
use Illuminate\Support\Facades\DB;
use App\Models\Servicos;


class RegistrosService
{
    public function create($request, $servico): AfugentFaunaExecRegistroModel
    {
        $grupoAmostrado = AtFaunaGrupoAmostradoModel::find($request['id_grupo_amostrado']);
        $nome_registro = $this->getNomeRegistro($grupoAmostrado);
        return AfugentFaunaExecRegistroModel::create([
            ...$request,
            'chave' => md5(uniqid()),
            'id_servico' => $servico->id,
            'nome_registro' => $nome_registro,
        ]);
    }

    public function update($request, $registro)
    {
        $registro->update([
            ...$request
        ]);
    }

    public function delete(AfugentFaunaExecRegistroModel $registro): void
    {
        $registro->delete();
    }

    public function getGrupoAmostrado()
    {
        return AtFaunaGrupoAmostradoModel::all();
    }

    public function getFrenteSupressao($servico)
    {
        return AfugentFaunaExecFrenteModel::where('id_servico', $servico->id)->get();
    }

    public function getFormaRegistro()
    {
        return AfugentFaunaFormaRegistroModel::all();
    }

    public function getUfs()
    {
        return Uf::all();
    }

    public function getRegistros($servico)
    {
        return AfugentFaunaExecRegistroModel::leftJoin('afugent_fauna_exec_frente', 'afugent_fauna_exec_registro.id_frente', '=', 'afugent_fauna_exec_frente.id')
            ->leftJoin('at_fauna_grupo_amostrado', 'afugent_fauna_exec_registro.id_grupo_amostrado', '=', 'at_fauna_grupo_amostrado.id')
            ->leftJoin('afugent_fauna_forma_registro', 'afugent_fauna_exec_registro.id_forma_registro', '=', 'afugent_fauna_forma_registro.id')
            ->leftJoin('afugent_fauna_tipo_registro', 'afugent_fauna_exec_registro.id_tipo_registro', '=', 'afugent_fauna_tipo_registro.id')
            ->leftJoin('afugent_fauna_destinacao_registro', 'afugent_fauna_exec_registro.id_destinacao_registro', '=', 'afugent_fauna_destinacao_registro.id')
            ->leftJoin('estados', 'afugent_fauna_exec_registro.id_estado', '=', 'estados.id')
            ->select(
                'afugent_fauna_exec_registro.*',
                'afugent_fauna_exec_frente.rodovia',
                'afugent_fauna_forma_registro.nome as nome_forma_registro',
                'afugent_fauna_tipo_registro.nome as nome_tipo_registro',
                'afugent_fauna_destinacao_registro.nome as nome_destinacao_registro',
                'estados.uf as uf',
                'at_fauna_grupo_amostrado.nome as nome_grupo',
                DB::raw("DATE_FORMAT(afugent_fauna_exec_registro.data_registro, '%d/%m/%Y') as data_registroF"),
                DB::raw("(SELECT nome FROM fauna_exec_status_conservacao WHERE id = afugent_fauna_exec_registro.id_status_conservacao_federal) as nome_status_conserv_federal"),
                DB::raw("(SELECT sigla FROM fauna_exec_status_conservacao WHERE id = afugent_fauna_exec_registro.id_status_conservacao_federal) as sigla_status_conserv_federal"),
                DB::raw("(SELECT nome FROM fauna_exec_status_conservacao WHERE id = afugent_fauna_exec_registro.id_status_conservacao_iucn) as nome_status_conserv_iucn"),
                DB::raw("(SELECT sigla FROM fauna_exec_status_conservacao WHERE id = afugent_fauna_exec_registro.id_status_conservacao_iucn) as sigla_status_conserv_iucn")
            )
            ->where('afugent_fauna_exec_registro.id_servico', $servico->id)
            ->paginate(10);
    }

    public function getNomeRegistro($grupoAmostrado)
    {
        $nome_registro = 'AR.';

        // Obtendo a quantidade de registros para o grupo amostrado
        $qntd_reg = AfugentFaunaExecRegistroModel::select('id')
            ->where('id_grupo_amostrado', $grupoAmostrado->id)
            ->get();

        // Calculando o número do nome do registro
        $num_nome_registro = count($qntd_reg) + 1;

        // Adicionando a primeira letra do grupo amostrado
        if ($grupoAmostrado->id == '1') {
            $nome_registro .= 'A';
        } else if ($grupoAmostrado->id == '2') {
            $nome_registro .= 'H';
        } else {
            $nome_registro .= 'M';
        }

        // Adicionando a quantidade de registros + 1 de acordo com o grupo amostrado
        if (strlen($num_nome_registro) == 1) {
            $nome_registro .= '00' . $num_nome_registro;
        } else if (strlen($num_nome_registro) == 2) {
            $nome_registro .= '0' . $num_nome_registro;
        } else {
            $nome_registro .= $num_nome_registro;
        }

        return $nome_registro;
    }

    public function getStatusConservacaoFederal()
    {
        return DB::table('fauna_exec_status_conservacao')
            ->whereIn('id', [1, 7, 9, 10])
            ->get();
    }

    public function getStatusConservacaoIucn()
    {
        return DB::table('fauna_exec_status_conservacao')
            ->whereIn('id', [2, 3, 4, 5, 6, 7, 8, 9, 10])
            ->get();
    }

    public function storeFile($registro, $file)
    {
        $nome = $file->getClientOriginalName();
        $key = uniqid();
        $caminho = $file->storeAs('public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'Afuget_fauna' . DIRECTORY_SEPARATOR . 'Registro' . DIRECTORY_SEPARATOR . 'Img' . DIRECTORY_SEPARATOR . $key . $nome);

        return AfugentFaunaExecRegistroImagemModel::create([
            'chave' => $key,
            'id_registro' => $registro->id,
            'nome' => $nome,
            'caminho_imagem' => str_replace("public\\", "", $caminho)
        ]);
    }

    public function getRegistroFotografico($registro)
    {
        return AfugentFaunaExecRegistroImagemModel::where('id_registro', $registro->id)->get();
    }

    public function destroyRegistroFotografico($fotografia)
    {
        return $fotografia->delete();
    }

    public function graficos_monitora_afugentamento(Servicos $servico): array
    {
        $allRegistros = AfugentFaunaExecRegistroModel::with(['grupo_faunistico', 'formaRegistro', 'tipoRegistro', 'destinacaoRegistro'])
            ->where('id_servico', $servico->id)
            ->get();


        $especiesGroup = $allRegistros->filter(function ($registro) {
            return !empty($registro->especie);
        })->groupBy('especie');

        $sortedEspeciesGroup = $especiesGroup->sortByDesc(function ($grupo) {
            return $grupo->count();
        });

        return [
            'totalRegistros' => $allRegistros->count(),
            'especiesGroup' => $sortedEspeciesGroup,
            'chartDataPieAbundancia'  => $this->getChartDataPieAbundancia($allRegistros),
            'chartDataPieDiversidade' => $this->getChartDataPieDiversidade($allRegistros),
            'getChartDataPieTipoRegistro' => $this->getChartDataPieTipoRegistro($allRegistros),
            'getChartDataPieFormaRegistro' => $this->getChartDataPieFormaRegistro($allRegistros),
            'getChartDataPieFormaRegistroGrafico' => $this->getChartDataPieFormaRegistroGrafico($allRegistros),
            'chartDataBar2'           => $this->getChartDataBar2($especiesGroup)
        ];
    }

    private function getChartDataPieFormaRegistro($allRegistros): array
    {
        $chartData = $allRegistros->groupBy(function ($registro) {
            return $registro->formaRegistro
                ? $registro->formaRegistro->nome
                : 'Sem Forma';
        })->map(function ($grupoRegistros, $formaNome) {
            return [
                // Retorna o id da forma de registro do primeiro item do grupo (todos deverão ter o mesmo)
                'id'    => $grupoRegistros->first()->formaRegistro->id ?? null,
                'nome'  => $formaNome,
                'total' => $grupoRegistros->count(),
            ];
        })->sortBy('nome')->values()->all();

        return $chartData;
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
                    'backgroundColor' => ["#a6c48a", "#7d9c6d", "#b3c99c", "#d5dfb3"],
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
            $uniqueSpecies = $grupoRegistros->pluck('especie')
                ->map(function ($especie) {
                    // Remove somente os caracteres "." e espaço que estejam no final da string
                    return rtrim($especie, '. ');
                })
                ->unique();
               
            return [
                'grupo_faunistico' => $grupoNome,
                'total'            => $uniqueSpecies->count(),
            ];
        })->values();
        

        return [
            'labels' => $diversidade->pluck('grupo_faunistico')->toArray(),
            'datasets' => [
                [
                    'data' => $diversidade->pluck('total')->toArray(),
                    'backgroundColor' => ["#a6c48a", "#7d9c6d", "#b3c99c", "#d5dfb3"],
                    'borderColor' => "#ffffff",
                    'borderWidth' => 2,
                ],
            ],
        ];
    }

    private function getChartDataPieTipoRegistro($allRegistros): array
    {
        $tipoRegistro = $allRegistros->groupBy(function ($registro) {
            return $registro->tipoRegistro
                ? $registro->tipoRegistro->nome
                : 'Sem Tipo';
        })->map(function ($grupoRegistros, $tipoNome) {
            return [
                'tipo_registro' => $tipoNome,
                'total'         => $grupoRegistros->count(),
            ];
        })->values();

        return [
            'labels'   => $tipoRegistro->pluck('tipo_registro')->toArray(),
            'datasets' => [
                [
                    'data'            => $tipoRegistro->pluck('total')->toArray(),
                    'backgroundColor' => ["#a6c48a", "#7d9c6d", "#b3c99c", "#d5dfb3"],
                    'borderColor'     => "#ffffff",
                    'borderWidth'     => 2,
                ],
            ],
        ];
    }

    private function getChartDataPieFormaRegistroGrafico($allRegistros): array
    {
        $formaRegistro = $allRegistros->groupBy(function ($registro) {
            return $registro->formaRegistro
                ? $registro->formaRegistro->nome
                : 'Sem Forma';
        })->map(function ($grupoRegistros, $formaNome) {
            return [
                'forma_registro' => $formaNome,
                'total'          => $grupoRegistros->count(),
            ];
        })->values();

        return [
            'labels'   => $formaRegistro->pluck('forma_registro')->toArray(),
            'datasets' => [
                [
                    'data'            => $formaRegistro->pluck('total')->toArray(),
                    'backgroundColor' => ["#a6c48a", "#7d9c6d", "#b3c99c", "#d5dfb3"],
                    'borderColor'     => "#ffffff",
                    'borderWidth'     => 2,
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
