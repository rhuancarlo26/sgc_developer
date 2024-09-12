<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Registro\Services;

use App\Models\ServicoPassagemFaunaConfigPassagem;
use App\Models\ServicoPassagemFaunaExecCampanha;
use App\Models\ServicoPassagemFaunaExecRegistro;
use App\Models\ServicoPassagemFaunaExecStatusConservacao;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class RegistroService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoPassagemFaunaExecRegistro::class;


    public function index($servico, array $searchParams): array
    {
        return [];
    }

    public function create($servico)
    {
        return [
            'campanhas' => ServicoPassagemFaunaExecCampanha::where('id_servico', $servico->id)->get(),
            'passagens' => ServicoPassagemFaunaConfigPassagem::where('id_servico', $servico->id)->get(),
            'status_conservacoes' => ServicoPassagemFaunaExecStatusConservacao::all()
        ];
    }

    public function store(array $request)
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }

    public function update(array $request)
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
    }
}
