<?php

namespace App\Domain\Servico\SupressaoVegetacao\app\Services;

use App\Models\ServicoParecerSupressaoConfiguracao;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class FiscalService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoParecerSupressaoConfiguracao::class;

    public function enviaFiscal(array $post, int $id_servico): array
    {
        $post['fk_servico'] = $id_servico;
        if (!isset($post['id'])) {
            $response = $this->store($post);
        } else {
            $response = $this->update($post);
        }

        return $response;
    }

    private function update(array $post): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);
    }

    private function store(array $post): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $post);
    }

}
