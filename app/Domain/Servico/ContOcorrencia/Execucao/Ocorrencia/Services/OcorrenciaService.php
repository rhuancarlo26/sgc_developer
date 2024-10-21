<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Services;

use App\Models\Rodovia;
use App\Models\ServicoConOcorrSupervisaoExecOcorrenciaAnterior;
use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\ServicoConOcorrSupervicaoExecOcorrenciaRegistro;
use App\Models\ServicoConOcorrSupervisaoExecOcorrenciaHistorico;
use App\Models\ServicoConOcorrSupervisaoExecOcorrenciaVistoria;
use App\Models\ServicoConOcorrSupervisaoExecOcorrenciaVistoriaArquivoPrazo;
use App\Models\ServicoConOcorrSupervisaoExecOcorrenciaVistoriaImg;
use App\Models\ServicoContOcorrSupervisaoConfigLote;
use App\Models\Servicos;
use App\Models\Uf;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Carbon\Carbon;
use FontLib\Table\Type\post;
use Illuminate\Support\Facades\Storage;

class OcorrenciaService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoConOcorrOcorrenciSupervisaoExecOcorrencia::class;
    protected string $modelClassRegistro = ServicoConOcorrSupervicaoExecOcorrenciaRegistro::class;
    protected string $modelClassHistorico = ServicoConOcorrSupervisaoExecOcorrenciaHistorico::class;
    protected string $modelClassVistoria = ServicoConOcorrSupervisaoExecOcorrenciaVistoria::class;
    protected string $modelClassVistoriaImg = ServicoConOcorrSupervisaoExecOcorrenciaVistoriaImg::class;
    protected string $modelClassVistoriaArquivo = ServicoConOcorrSupervisaoExecOcorrenciaVistoriaArquivoPrazo::class;
    protected string $modelClassOcorrenciaAnterior = ServicoConOcorrSupervisaoExecOcorrenciaAnterior::class;


    public function index(Servicos $servico, array $searchParams): array
    {
        return [
            'ocorrencias' => $this->searchAllColumns(...$searchParams)
                ->with([
                    'lote',
                    'rodovia.uf',
                    'registros',
                    'historico.levantamento',
                    'ocorrencia_anterior',
                    'vistorias' => function ($query) {
                        $query->latest();
                    }
                ])
                ->where('id_servico', $servico->id)->where('envio_empresa', '=', 'Não')
                ->paginate()
                ->appends($searchParams),
            'ocorrencias_em_aberto' => $this->modelClass::with(['lote', 'rodovia.uf', 'registros', 'historico.levantamento'])
                ->where('id_servico', $servico->id)->where('tipo', 'RNC')->where('envio_empresa', '=', 'Não')->get()
        ];
    }

    public function create(array $post): array
    {
        return [
            'lotes' => ServicoContOcorrSupervisaoConfigLote::where('id_servico', $post['servico_id'])->get(),
            'rodovias' => Rodovia::with(['uf'])->get()
        ];
    }

    public function createVistoria(array $post): array
    {
        return [];
    }

    public function store(array $post): array
    {
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $post);

        $this->dataManagement->create(entity: $this->modelClassHistorico, infos: [
            'id_ocorrencia' => $response['model']['id'],
            'id_ocorrencia_levantamento' => 1
        ]);

        return $response;
    }

    public function storeRegistro(array $post): array
    {
        $nome = $post['arquivo']->getClientOriginalName();
        $caminho = $post['arquivo']->storeAs('public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'ConOcorr' . DIRECTORY_SEPARATOR . 'Registro' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

        return $this->dataManagement->create(entity: $this->modelClassRegistro, infos: [
            'id_ocorrencia' => $post['id_ocorrencia'],
            'nome' => $nome,
            'caminho_arquivo' => str_replace("public\\", "", $caminho)
        ]);
    }

    public function storeVistoria(array $post)
    {
        $dataOcorrencia = Carbon::parse($post['ocorrencia']['data_ocorrencia'])->addDays($post['ocorrencia']['prazo']);
        $dataVistoria = Carbon::parse($post['vistoria']['data_vistoria']);

        $this->dataManagement->create(entity: $this->modelClassHistorico, infos: [
            'id_ocorrencia' => $post['ocorrencia']['id'],
            'id_ocorrencia_levantamento' => 3
        ]);

        // verifica se o prazo é um caractere numérico
        if (ctype_digit($post['ocorrencia']['prazo'])) {

            if ($post['vistoria']['corrigido'] === 'Não') {
                // se vistoria_dias for maior que prazo da ocorrencia então, CRIA-SE UMA NOVA OCORRENCIA
                if ($dataVistoria->greaterThanOrEqualTo($dataOcorrencia)) {
                    // se o tipo_vistoria for 'RNC' e tipo da ocorrencia for 'ROA'
                    if ($post['vistoria']['tipo_vistoria'] != $post['ocorrencia']['tipo']) {
                        // atualizando o status da ocorrencia anterior como 'não atendida'
                        $this->dataManagement->update(entity: $this->modelClass, infos: [
                            ...$post['ocorrencia'],
                            'status' => 'Não Antendida'
                        ], id: $post['ocorrencia']['id']);
                        // inserindo na nova ocorrencia alguns dados vindos da vistoria

                        $ocorrencia = [
                            ...$post['ocorrencia'],
                            //                            'intensidade' => $post['vistoria']['intensidade_vistoria'],
                            //                            'tipo' => $post['vistoria']['tipo_vistoria'],
                            'status' => 'Em aberto',
                            'envio_empresa' => 'Não',
                            //                            'zona' => $post['ocorrencia']['zona'] ?? '-',
                            //                            'possivel_causa' => $post['ocorrencia']['possivel_causa'] ?? '-',
                            //                            'descricao_causa' => $post['ocorrencia']['descricao_causa'] ?? '-',
                            //                            'aprovado_rnc' => $post['ocorrencia']['aprovado_rnc'] ?? '-',
                        ];
                        //                        if (array_key_exists('acordo_prazo', $post['vistoria'])) {
                        //                            $ocorrencia['prazo'] = $post['vistoria']['prazo_vistoria'];
                        //                            $ocorrencia['dias_restantes'] = $post['vistoria']['prazo_vistoria'];
                        //                        } else {
                        //                            $ocorrencia['prazo'] = $post['ocorrencia']['prazo'];
                        //                            $ocorrencia['dias_restantes'] = $post['ocorrencia']['prazo'];
                        //                        }
                        $lastOcorrencia = $this->modelClass::where('id_servico', $post['ocorrencia']['id_servico'])->where('tipo', $post['ocorrencia']['tipo'])->orderby('num_por_servico', 'DESC')->first() ?? 0;

                        $ocorrencia['nome_id'] = $post['ocorrencia']['tipo'] . '.' . str_pad(($lastOcorrencia->num_por_servico ?? 0) + 1, 2, '0', STR_PAD_LEFT) . '.' . $post['ocorrencia']['rodovia']['rodovia'] . '-' . Uf::find($post['ocorrencia']['rodovia']['estados_id'])->uf;
                        $ocorrencia['num_por_servico'] = str_pad(($lastOcorrencia->num_por_servico ?? 0) + 1, 2, '0', STR_PAD_LEFT);

                        $novaOcorrencia = $this->dataManagement->create(entity: $this->modelClass, infos: $ocorrencia);

                        $this->dataManagement->create(entity: $this->modelClassOcorrenciaAnterior, infos: [
                            'id_ocorrencia_vigente' => $novaOcorrencia['model']['id'],
                            'id_ocorrencia_anterior' => $post['ocorrencia']['id']
                        ]);

                        $imagens = $this->modelClassRegistro::where('id_ocorrencia', $post['ocorrencia']['id'])->get();

                        foreach ($imagens as $imagem) {
                            $this->dataManagement->create(entity: $this->modelClassRegistro, infos: [
                                ...$imagem,
                                'id_ocorrencia' => $novaOcorrencia['model']['id']
                            ]);
                        }

                        $this->dataManagement->create(entity: $this->modelClassHistorico, infos: [
                            'id_ocorrencia' => $novaOcorrencia['model']['id'],
                            'id_ocorrencia_levantamento' => 1
                        ]);
                    } else {
                        //                      se o tipo_vistoria for igual ao tipo da ocorrencia, então atualiza a ocorrencia
                        $ocorrencia = [
                            ...$post['ocorrencia'],
                            //                            'intensidade' => $post['ocorrencia']['intensidade'],
                            'envio_empresa' => 'Não'
                        ];

                        //                        if (array_key_exists('acordo_prazo', $post['vistoria'])) {
                        //                            $ocorrencia['prazo'] = $post['vistoria']['prazo_vistoria'];
                        //                            $ocorrencia['dias_restantes'] = $post['vistoria']['prazo_vistoria'];
                        //                        }

                        $this->dataManagement->update(entity: $this->modelClass, infos: $ocorrencia, id: $post['ocorrencia']['id']);
                    }
                } else {
                    //                    se vistoria_dias for menor que prazo da ocorrencia então atualiza a ocorrencia
                    $ocorrencia = [
                        ...$post['ocorrencia'],
                        'intensidade' => $post['vistoria']['intensidade_vistoria'],
                        'envio_empresa' => 'Não'
                    ];

                    if (array_key_exists('acordo_prazo', $post['vistoria'])) {
                        if ($post['vistoria']['tipo_vistoria'] === 'RNC') {
                            $ocorrencia['prazo'] = $post['vistoria']['prazo_vistoria'];
                            $ocorrencia['dias_restantes'] = $post['vistoria']['prazo_vistoria'];
                        } else {
                            //                            $ocorrencia['dias_restantes'] = $post['vistoria']['dias_restantes'];
                        }
                    }

                    $this->dataManagement->update(entity: $this->modelClass, infos: $ocorrencia, id: $post['ocorrencia']['id']);
                }
            } else {
                //                se a ocorrencia for corrigida
                $ocorrencia = [
                    ...$post['ocorrencia'],
                    'envio_empresa' => 'Não',
                    'status' => 'Atendida'
                ];

                $this->dataManagement->update(entity: $this->modelClass, infos: $ocorrencia, id: $post['ocorrencia']['id']);

                $this->dataManagement->create(entity: $this->modelClassHistorico, infos: [
                    'id_ocorrencia' => $post['ocorrencia']['id'],
                    'id_ocorrencia_levantamento' => 9
                ]);
            }
        } else {
            //            se o prazo não um caractere numérico a ocorrencia será um 'RNC' automaticamente atualizar o campo de envio
            $ocorrencia = [
                ...$post['ocorrencia'],
                'intensidade' => $post['vistoria']['intensidade_vistoria'],
                'envio_empresa' => 'Não'
            ];

            //            e atualizar o campo de status da ocorrencia como 'atendida' se estiver corrigida
            if ($post['vistoria']['corrigido'] === 'Sim') {
                $ocorrencia['status'] = 'Atendida';

                $this->dataManagement->create(entity: $this->modelClassHistorico, infos: [
                    'id_ocorrencia' => $post['ocorrencia']['id'],
                    'id_ocorrencia_levantamento' => 9
                ]);
            } else {
                $ocorrencia['prazo'] = $post['vistoria']['prazo_vistoria'];
                $ocorrencia['dias_restantes'] = $post['vistoria']['prazo_vistoria'];
            }

            $this->dataManagement->update(entity: $this->modelClassHistorico, infos: $ocorrencia, id: $post['ocorrencia']['id']);
        }

        return $this->dataManagement->create(entity: $this->modelClassVistoria, infos: $post['vistoria']);
    }

    public function storeVistoriaImagem(array $post)
    {
        $response = [
            'request' => [
                'type' => 'error',
                'content' => 'Falha ao cadastrar!'
            ]
        ];

        foreach ($post['imagens'] as $key => $value) {
            $infos = [];

            $infos['nome'] = $value->getClientOriginalName();
            $infos['caminho_arquivo'] = $value->storeAs('public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'ConOcorr' . DIRECTORY_SEPARATOR . 'Vistoria' . DIRECTORY_SEPARATOR . 'Imagem' . DIRECTORY_SEPARATOR . uniqid() . '_' . $key . '_' . $infos['nome']);

            $response = $this->dataManagement->create(entity: $this->modelClassVistoriaImg, infos: [
                'id_vistoria' => $post['id_vistoria'],
                'nome' => $infos['nome'],
                'caminho_arquivo' => str_replace("public\\", "", $infos['caminho_arquivo'])
            ]);
        }

        return $response;
    }

    public function storeVistoriaArquivo(array $post)
    {
        $response = [
            'request' => [
                'type' => 'error',
                'content' => 'Falha ao cadastrar!'
            ]
        ];

        foreach ($post['arquivos'] as $key => $value) {
            $infos = [];

            $infos['nome'] = $value->getClientOriginalName();
            $infos['caminho_arquivo'] = $value->storeAs('public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'ConOcorr' . DIRECTORY_SEPARATOR . 'Vistoria' . DIRECTORY_SEPARATOR . 'Arquivo' . DIRECTORY_SEPARATOR . uniqid() . '_' . $key . '_' . $infos['nome']);

            $response = $this->dataManagement->create(entity: $this->modelClassVistoriaArquivo, infos: [
                'id_vistoria' => $post['id_vistoria'],
                'nome' => $infos['nome'],
                'caminho_arquivo' => str_replace("public\\", "", $infos['caminho_arquivo'])
            ]);
        }

        return $response;
    }

    public function enviarOcorrencia(array $post)
    {
        foreach ($post['ocorrencias'] as $item) {
            if ($item['tipo'] == 'RNC') {
                $data['aprovado_rnc'] = 'Em análise';
                $data['envio_empresa'] = 'Sim';
                $data['envio_fiscal'] = 'Sim';
                $tipo = 5;
            } else {
                $data['envio_empresa'] = 'Sim';
                $data['envio_fiscal'] = 'Sim';
                $tipo = 4;
            }

            $response = $this->dataManagement->update(entity: $this->modelClass, infos: $data, id: $item['id']);

            $this->dataManagement->create(entity: $this->modelClassHistorico, infos: [
                'id_ocorrencia' => $item['id'],
                'id_ocorrencia_levantamento' => $tipo
            ]);
        }

        return $response;
    }

    public function update(array $post): array
    {
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);

        $this->dataManagement->create(entity: $this->modelClassHistorico, infos: [
            'id_ocorrencia' => $post['id'],
            'id_ocorrencia_levantamento' => 2
        ]);

        return $response;
    }

    public function updateVistoria(array $post): array
    {
        return $this->dataManagement->update(entity: $this->modelClassVistoria, infos: $post['vistoria'], id: $post['vistoria']['id']);
    }

    public function destroy(array $post): array
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $post['id']);
    }

    public function destroyRegistro($registro): array
    {
        Storage::delete($registro->caminho_arquivo);

        return $this->dataManagement->delete(entity: $this->modelClassRegistro, id: $registro->id);
    }

    public function destroyVistoria(array $post): array
    {
        return $this->dataManagement->delete(entity: $this->modelClassVistoria, id: $post['id']);
    }

    public function destroyVistoriaImagem(ServicoConOcorrSupervisaoExecOcorrenciaVistoriaImg $imagem): array
    {
        Storage::delete('public' . DIRECTORY_SEPARATOR . $imagem->caminho_arquivo);

        return $this->dataManagement->delete(entity: $this->modelClassVistoriaImg, id: $imagem->id);
    }

    public function destroyVistoriaArquivo(ServicoConOcorrSupervisaoExecOcorrenciaVistoriaArquivoPrazo $arquivo): array
    {
        Storage::delete('public' . DIRECTORY_SEPARATOR . $arquivo->caminho_arquivo);

        return $this->dataManagement->delete(entity: $this->modelClassVistoriaArquivo, id: $arquivo->id);
    }
}
