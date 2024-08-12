<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\app\Utils;

use App\Models\ServicoParecerAfugentamentoConfiguracao;

class ConfigucacaoParecer
{
    public function get(int $id_servico): array
    {
        return ['aprovacao' => ServicoParecerAfugentamentoConfiguracao::where('fk_servico', $id_servico)->first()];
    }
}
