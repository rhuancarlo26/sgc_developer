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

    public function buscarImportadores(): array
    {
        $modulos = Modulo::all();
        $importadores = ModuloImportador::with('modulo')->paginate(10);

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
