<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Services;

use App\Models\Modulo;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\ModulosHandler;
use App\Shared\Traits\Searchable;

class ConfiguracoesModulosService extends BaseModelService
{
    use ModulosHandler, Searchable;

    protected string $modelClass = Modulo::class;

    public function buscarModulos($searchParams): array
    {
        // $modulos = Modulo::paginate(10);
        $modulos = $this->searchAllColumns(...$searchParams)
            ->paginate(10)
            ->appends($searchParams);

        return [
            'modulos' => $modulos,
            'tipos' => $this->buscarParams()
        ];
    }

    public function store(array $data): array
    {
        if (!is_null($data['planilha_modelo'])) {
            $data = $this->addArquivo($data);
        }

        return $this->dataManagement->create(entity: $this->modelClass, infos: $data);
    }

    public function update(Modulo $modulo, array $data): array
    {
        if (!is_null($data['planilha_modelo'])) {
            $data = $this->addArquivo($data);
        }

        $dataManagement = $this->dataManagement->update(entity: $this->modelClass, infos: $data, id: $modulo->id);
        return $dataManagement['request'];
    }

    private function addArquivo(array $data): array
    {
        $arquivo = $data['planilha_modelo'];

        $nomeArquivo = $arquivo->getClientOriginalName();
        $nomeCaminho = 'Modulos' . DIRECTORY_SEPARATOR . uniqid() .  '_' . $nomeArquivo;
        $arquivo->storeAs('public' . DIRECTORY_SEPARATOR . $nomeCaminho);

        $data['nome_planilha_modelo'] = $nomeArquivo;
        $data['caminho_planilha_modelo'] = $nomeCaminho;

        return $data;
    }
}
