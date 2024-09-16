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
        $response = [
            'model' => null,
            'request' => [
                'type' => 'error',
                'content' => 'Falha ao cadastrar!',
                'error' => ''
            ]
        ];

        $ultimaPassagemFauna = $this->modelClass::where('id_servico', $servico->id)->orderby('num_por_servico', 'DESC')->first();
        $num_por_servico = $ultimaPassagemFauna ? $ultimaPassagemFauna->num_por_servico : 0;

        $passagemFaunaImport = new PassagemFaunaImport();

        $passagens = Excel::toCollection($passagemFaunaImport, $arquivo)->first();

        foreach ($passagens as $passagemFauna) {
            $num_por_servico = $num_por_servico + 1;

            $response = $this->dataManagement->create(entity: $this->modelClass, infos: [
                ...$passagemFauna,
                'id_servico' => $servico->id,
                'num_por_servico' => $num_por_servico,
                'nome_id' => 'PF' . str_pad($num_por_servico, 2, '0', STR_PAD_LEFT)
            ]);
        }

        return $response;
    }

    public function destroy($passagem_fauna)
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $passagem_fauna->id);
    }

    public function update(array $post)
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);
    }
}
