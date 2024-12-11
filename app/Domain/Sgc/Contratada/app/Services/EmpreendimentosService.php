<?php

namespace App\Domain\Sgc\Contratada\app\Services;

use App\Models\DashexcelEmpreendimentos;
use App\Shared\Abstract\BaseModelService;

class EmpreendimentosService extends BaseModelService {

    protected string $modelClass = DashexcelEmpreendimentos::class;
    
    public function salvarEmpreendimento($request) {

        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

        return [
          'request' => $response['request']
        ];
    }

    public function atualizarEmpreendimento($id, $data)
    {
      // Encontre o empreendimento pelo ID
      $empreendimento = DashexcelEmpreendimentos::findOrFail($id);

      // Atualize os dados do empreendimento
      $empreendimento->update($data);

      return "Empreendimento atualizado com sucesso!";
    }

    public function buscarEmpreendimentoPorId($id)
    {
        return DashexcelEmpreendimentos::find($id); // Substitua "findOrFail" por "find" para retornar null se não existir
    }
}