<?php

namespace App\Domain\Servico\PMQA\app\Utils;

use App\Models\ServicoParecerPMQAConfiguracao;

class ConfigucacaoParecer
{
    public function get(int $id_servico): array
    {
        return ['aprovacao' => ServicoParecerPMQAConfiguracao::where('fk_servico', $id_servico)->first()];
    }
}
