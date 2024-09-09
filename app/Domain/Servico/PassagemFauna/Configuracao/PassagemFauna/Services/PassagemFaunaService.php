<?php

namespace App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Services;

use App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Imports\PassagemFaunaImport;
use App\Models\ServicoPassagemFaunaConfigPassagem;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Maatwebsite\Excel\Facades\Excel;

class PassagemFaunaService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoPassagemFaunaConfigPassagem::class;

    public function index(Servicos $servico, array $searchParams): array
    {
        return [
            'passagens' => $this->searchAllColumns(...$searchParams)
                ->where('id_servico', '=', $servico->id)
                ->paginate()
                ->appends($searchParams)
        ];
    }

    public function importar($servico, $arquivo)
    {
        $passagemFaunaImport = new PassagemFaunaImport();

        $passagens = Excel::toCollection($passagemFaunaImport, $arquivo)->first();

        dd($passagens);

        return ['type' => 'success', 'content' => 'Registro cadastrado!'];
    }
}
