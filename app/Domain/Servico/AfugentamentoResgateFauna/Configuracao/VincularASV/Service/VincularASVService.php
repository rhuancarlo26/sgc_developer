<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularASV\Service;

use App\Models\AfugentFaunaConfigASVModel;
use App\Models\Licenca;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class VincularASVService
{
    use Searchable, Deletable;


    public function create($licenca, $servico): AfugentFaunaConfigASVModel
    {
        $var = AfugentFaunaConfigASVModel::where('id_servico', $servico->id)->where('id_licenca', $licenca->id)->exists();

        if ($var) {
            throw new \Exception('Este serviço já está vinculado a esta licença');
        }

        return AfugentFaunaConfigASVModel::create([
            'chave' => md5(uniqid()),
            'id_servico' => $servico->id,
            'id_licenca' => $licenca->id
        ]);
    }

    public function delete(AfugentFaunaConfigASVModel $licenca): void
    {
        $licenca->delete();
    }
}
