<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularABIO\Service;

use App\Models\AfugentFaunaConfigABIOModel;

class VincularABIOService
{
    public function create($licenca, $servico): AfugentFaunaConfigABIOModel
    {
        $var = AfugentFaunaConfigABIOModel::where('id_servico', $servico->id)->where('id_licenca', $licenca->id)->exists();
        
        if ($var) {
            throw new \Exception('Este serviço já está vinculado a esta licença');
        }

        return AfugentFaunaConfigABIOModel::create([
            'chave' => md5(uniqid()),
            'id_servico' => $servico->id,
            'id_licenca' => $licenca->id
        ]);
    }

    public function delete(AfugentFaunaConfigABIOModel $licenca): void
    {
        $licenca->delete();
    }
}