<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Domain\Modulos\Importador\Services\DadosImportadorService;
use App\Models\ModuloImportador;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DadosImportadorController extends Controller
{
    public function __construct(
        private DadosImportadorService $service
    ) {
        //
    }

    public function buscarDados(ModuloImportador $importador, Request $request): JsonResponse
    {
        $data = $this->service->buscarDados($importador, $request);
        return response()->json($data);
    }

    public function importarPlanilha(ModuloImportador $importador, Request $request)
    {
        $request->validate([
            'arquivo' => ['required', 'file', 'mimes:xlsx,csv'],
        ]);

        if ($importador->dadosJson()->exists()) {
            return back()->with('message', [
                'type' => 'warning',
                'content' => 'Já existem dados importados. Exclua os dados atuais antes de importar novamente.',
            ]);
        }

        $this->service->importarPlanilha($importador, $request->file('arquivo'));

        return back()->with('message', [
            'type' => 'success',
            'content' => 'Planilha importada com sucesso!',
        ]);
    }

    public function excluirDados(ModuloImportador $importador): JsonResponse
    {
        $totalExcluido = $this->service->excluirDados($importador);

        return response()->json([
            'success' => true,
            'message' => 'Dados da planilha excluídos com sucesso.',
            'total_excluido' => $totalExcluido,
        ]);
    }
}
