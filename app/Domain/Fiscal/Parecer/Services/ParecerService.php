<?php

namespace App\Domain\Fiscal\Parecer\Services;

use App\Models\ServicoParecer;
use App\Models\ServicoParecerAfugentamentoConfiguracao;
use App\Models\ServicoParecerPMQAConfiguracao;
use App\Models\ServicoParecerSupressaoConfiguracao;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use App\Models\Contrato;

class ParecerService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Contrato::class;

    public function emiteParecerServico($post): array
    {
        $this->dataManagement->update(entity: ServicoParecer::class, infos: $post, id: $post['id_parecer']);
        $response = $this->dataManagement->update(entity: Servicos::class, infos: $post, id: $post['fk_servico']);

        return [
            'request' => $response['request']
        ];
    }

    public function emiteParecerConfigPMQA($post): array
    {
        $response = $this->dataManagement->update(entity: ServicoParecerPMQAConfiguracao::class, infos: $post, id: $post['id']);

        return [
            'request' => $response['request']
        ];
    }

    public function emiteParecerConfigSupressao($post): array
    {
        $response = $this->dataManagement->update(entity: ServicoParecerSupressaoConfiguracao::class, infos: $post, id: $post['id']);

        return [
            'request' => $response['request']
        ];
    }

    public function emiteParecerConfigAfugentamento($post): array
    {
        $response = $this->dataManagement->update(entity: ServicoParecerAfugentamentoConfiguracao::class, infos: $post, id: $post['id']);

        return [
            'request' => $response['request']
        ];
    }
}
