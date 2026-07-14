<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\app\Services;

use App\Models\ServicoParecerAfugentamentoConfiguracao;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use App\Domain\Servico\app\Utils\EnviaFiscalUtils;

class FiscalService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoParecerAfugentamentoConfiguracao::class;

    public function __construct(private readonly EnviaFiscalUtils $enviaFiscalUtils)
    {
    }

    public function enviaFiscal(array $post, int $id_servico): array
    {
        $post['fk_servico'] = $id_servico;
        if (!isset($post['id'])) {
            $response = $this->enviaFiscalUtils->store($post, $this->modelClass);
        } else {
            $response = $this->enviaFiscalUtils->update($post, $this->modelClass);
        }

        return $response;
    }

}
