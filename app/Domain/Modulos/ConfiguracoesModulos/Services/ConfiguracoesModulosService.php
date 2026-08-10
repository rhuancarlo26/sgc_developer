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
            ->with(['contrato:id,numero_contrato,contratada'])
            ->paginate(10)
            ->appends($searchParams);

        return [
            'modulos' => $modulos,
            'tipos' => $this->buscarParams()
        ];
    }

    public function store(array $data): array
    {
        $data = $this->normalizarPmqa($data);

        if (!empty($data['planilha_modelo'])) {
            $data = $this->addArquivo($data);
        }

        $modulo = Modulo::create($data);

        return [
            'model' => $modulo,
            'request' => [
                'type' => 'success',
                'content' => 'Módulo cadastrado com sucesso!',
            ],
        ];
    }

    public function update(Modulo $modulo, array $data): array
    {
        $data = $this->normalizarPmqa($data);

        if (!empty($data['planilha_modelo'])) {
            $data = $this->addArquivo($data);
        }

        $modulo->update($data);

        return [
            'type' => 'success',
            'content' => 'Módulo atualizado com sucesso!',
        ];
    }

    private function normalizarPmqa(array $data): array
    {
        $data['pmqa'] = (int) ($data['pmqa'] ?? 0);

        if ($data['pmqa'] !== 1) {
            $data['contrato_id'] = null;
        }

        return $data;
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
