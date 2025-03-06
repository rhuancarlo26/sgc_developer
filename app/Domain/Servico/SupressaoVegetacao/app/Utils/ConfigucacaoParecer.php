<?php

namespace App\Domain\Servico\SupressaoVegetacao\app\Utils;

use App\Models\ServicoParecerSupressaoConfiguracao;

class ConfigucacaoParecer
{
    public function get(int $id_servico): array
    {
        return ['aprovacao' => ServicoParecerSupressaoConfiguracao::where('fk_servico', $id_servico)->first()];
    }
}
