<?php

namespace App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Services;

use App\Models\Licenca;
use App\Models\ServicoPassagemFaunaConfigAbio;
use App\Models\ServicoPassagemFaunaConfigAbioRet;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class VincularABIOService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoPassagemFaunaConfigAbio::class;
    protected string $modelClassRET = ServicoPassagemFaunaConfigAbioRet::class;
    protected string $modelClassLicenca = Licenca::class;

    public function index(Servicos $servico, array $searchParams): array
    {
        return [
            'abios' => $this->searchAllColumns(...$searchParams)
                ->with([
                    'servico',
                    'licenca.tipo_rel',
                    'abio_ret',
                ])
                ->where('id_servico', '=', $servico->id)
                ->paginate()
                ->appends($searchParams)
        ];
    }

    public function store(array $post)
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $post);
    }

    public function storeRET(array $post)
    {
        $nome = $post['arquivo']->getClientOriginalName();
        $caminho = $post['arquivo']->storeAs('Servico' . DIRECTORY_SEPARATOR . 'PassagemFauna' . DIRECTORY_SEPARATOR . 'Configuracao' . DIRECTORY_SEPARATOR . 'VincularABIO' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

        return $this->dataManagement->create(entity: $this->modelClassRET, infos: [
            'id_abio' => $post['id_abio'],
            'nome' => $nome,
            'caminho_ret' => $caminho
        ]);
    }

    public function destroyRet(object $abio_ret): array
    {
        \Storage::delete($abio_ret->caminho_ret);

        return $this->dataManagement->delete(entity: $this->modelClassRET, id: $abio_ret->id);
    }

    public function destroyABIO(int $abio_id)
    {
        $abio_ret = $this->modelClassRET::where('id_abio', '=', $abio_id)->first();

        if ($abio_ret) {
            \Storage::delete($abio_ret->caminho_ret);
        }

        return $this->dataManagement->delete(entity: $this->modelClass, id: $abio_id);
    }

    public function buscarLicenca(string $numero_licenca)
    {
        return $this->modelClassLicenca::with(['segmentos'])->where('numero_licenca', '=', $numero_licenca)->firstOrFail();
    }
}
