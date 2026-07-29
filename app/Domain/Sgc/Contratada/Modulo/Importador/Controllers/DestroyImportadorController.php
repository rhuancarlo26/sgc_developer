<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Domain\Modulos\Importador\Services\DestroyService;
use App\Models\ModuloImportador;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DestroyImportadorController extends Controller
{
    public function __construct(private DestroyService $service)
    {
        // 
    }

    public function destroy(ModuloImportador $importador): RedirectResponse
    {
        $dataManagement = $this->service->destroy($importador);
        return to_route('modulos.importador.index')->with('message', $dataManagement);
    }
}
