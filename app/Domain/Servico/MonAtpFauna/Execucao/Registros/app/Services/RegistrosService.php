<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Services;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Imports\PMQAPontoImport;
use App\Models\AtFaunaExecucaoRegistro;
use App\Models\AtFaunaExecucaoRegistroImagem;
use App\Models\Licenca;
use App\Models\ServicoMonAtpFaunaVincularABIO;
use App\Models\ServicoPmqaPonto;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use App\Shared\Utils\ArquivoUtils;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class RegistrosService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = AtFaunaExecucaoRegistro::class;

    public function index(Servicos $servico, array $searchParams)
    {
        return $this->searchAllColumns(...$searchParams)
            ->select([
                'at_fauna_execucao_registro.*',
                'licencas_br.rodovia',
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
            ->leftJoin('estados', 'estados.id', '=', 'at_fauna_execucao_registro.fk_estado')
            ->join('at_fauna_grupo_amostrado AS fga', 'fga.id', '=', 'at_fauna_execucao_registro.fk_grupo_amostrado')
            ->where('at_fauna_execucao_registro.fk_servico', $servico->id)
            ->paginate();
    }

    public function store(array $request): array
    {
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

        if($request['arquivo']) {
            $this->saveImage($request['arquivo'], $response['model']['id']);
        }

        return $response;
    }

    public function update(array $request): array
    {
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

        if($request['arquivo']) {
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
}
