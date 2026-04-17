<?php

namespace App\Domain\Modulos\Importador\Services;

use App\Models\Contrato;
use App\Models\Modulo;
use App\Models\ModuloImportador;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\ModulosHandler;
use App\Shared\Traits\Searchable;
use Illuminate\Database\Eloquent\Collection;

class ImportadorService extends BaseModelService
{
    use ModulosHandler, Searchable;

    protected string $modelClass = ModuloImportador::class;

    public function buscarImportadores(array $searchParams): array
    {
        $modulos = Modulo::all();
        // $importadores = ModuloImportador::with('modulo')->paginate(10);
        $importadores = $this->searchAllColumns(...$searchParams)
            ->with('modulo')
            ->paginate(10)
            ->appends($searchParams);

        $importadores->getCollection()->each(function ($item) {
            $item->append('status_formatado');
            $item->append('revisao');
        });

        return [
            'modulos' => $modulos,
            'importadores' => $importadores,
        ];
    }

    public function buscarModulos(): Collection
    {
        return Modulo::all();
    }

    public function buscarContratos(): Collection
    {
        return Contrato::all();
    }
}
