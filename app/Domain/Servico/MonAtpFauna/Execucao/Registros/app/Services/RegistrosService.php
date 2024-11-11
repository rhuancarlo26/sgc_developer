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
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class RegistrosService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = AtFaunaExecucaoRegistro::class;

    public function index(Servicos $servico, array $searchParams, bool $paginate = true): LengthAwarePaginator|Builder
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
                'type' => 'error',
                'content' => 'Falha ao cadastrar!',
                'error' => ''
            ]
        ];

        $passagemFaunaImport = new RegistroImport();

        $registros = Excel::toCollection($passagemFaunaImport, $post['arquivo'])->first();

        foreach ($registros as $registro) {
            $uf = Uf::where('nome', '=', $registro['estado'])->orWhere('uf', '=', $registro['estado'])->first();
            $classe = ['Avifauna', 'Herpetofauna', 'Mastofauna'];

            $response = $this->dataManagement->create(entity: $this->modelClass, infos: [
                ...$registro,
                'fk_estado' => $uf->id ?? null,
                'fk_campanha' => $post['campanha_id'],
                'fk_servico' => $post['servico_id'],
                'sentido' => strtoupper($registro['sentido'][0] ?? ''),
                'margem' => strtoupper($registro['margem'][0] ?? ''),
                'data_registro' => $this->getDateYMD($registro['data_registro'])
            ]);
        }

        return $response;
    }

    private function getDateYMD(string|int $date): string
    {

        if (is_string($date) && str_contains($date, '/')) {
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        }

        if (is_string($date) && str_contains($date, '-')) {
            return Carbon::parse($date)->format('Y-m-d');
        }

        if (is_int($date)) {
            return Date::excelToDateTimeObject($date)->format('Y-m-d');
        }

        if ($date instanceof Carbon) {
            return $date->format('Y-m-d');
        }
    }
}
