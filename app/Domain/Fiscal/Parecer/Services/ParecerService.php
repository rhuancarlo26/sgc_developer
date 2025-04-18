<?php

namespace App\Domain\Fiscal\Parecer\Services;

use App\Models\AtFaunaParecerConfiguracao;
use App\Models\ServicoParecer;
use App\Models\ServicoParecerAfugentamentoConfiguracao;
use App\Models\ServicoParecerPMQAConfiguracao;
use App\Models\ServicoParecerSupressaoConfiguracao;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use App\Models\Contrato;
use App\Models\ServicoContOcorrSupervisaoParecerConfiguracao;
use App\Models\ServicoPassagemFaunaParecerConfiguracao;

class ParecerService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Contrato::class;

    public function emiteParecerServico($post): array
    {
        if (!isset($post['id_parecer'])) {
            $this->dataManagement->create(entity: ServicoParecer::class, infos: $post);
        } else {
            $this->dataManagement->update(entity: ServicoParecer::class, infos: $post, id: $post['id_parecer']);
        }
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

    public function emiteParecerConfigSupervisao($post): array
    {
        $response = $this->dataManagement->update(entity: ServicoContOcorrSupervisaoParecerConfiguracao::class, infos: $post, id: $post['id']);

        return [
            'request' => $response['request']
        ];
    }

    public function emiteParecerConfigPassagemFauna($post): array
    {
        $response = $this->dataManagement->update(entity: ServicoPassagemFaunaParecerConfiguracao::class, infos: $post, id: $post['id']);

        return [
            'request' => $response['request']
        ];
    }

    public function emiteParecerConfigAtropelamentoFauna($post): array
    {
        $response = $this->dataManagement->update(entity: AtFaunaParecerConfiguracao::class, infos: $post, id: $post['id']);

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
