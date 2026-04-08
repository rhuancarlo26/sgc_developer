<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Services;

use App\Models\Modulo;

class ConfiguracoesModulosService
{
    public function buscarModulos(): array
    {
        $modulos = Modulo::paginate(10);
        return [
            'modulos' => $modulos
        ];
    }
}
