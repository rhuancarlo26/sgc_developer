<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Services;

use App\Models\Rodovia;
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

    public function index(Servicos $servico, array $searchParams): array
    {
        return [
            'ocorrencias' => $this->searchAllColumns(...$searchParams)
                ->with(['lote', 'rodovia.uf', 'registros', 'historico.levantamento', 'vistorias'])
                ->where('id_servico', $servico->id)
                ->paginate()
                ->appends($searchParams),
            'ocorrencias_em_aberto' => $this->modelClass::with(['lote', 'rodovia.uf', 'registros', 'historico.levantamento'])
                ->where('id_servico', $servico->id)->where('rnc_direto', 'RNC')->get()
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
                $data['envio_empresa'] = 'Não';
                $data['envio_fiscal'] = 'Sim';
                $tipo = 5;
            } else {
                $data['envio_empresa'] = 'Sim';
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
