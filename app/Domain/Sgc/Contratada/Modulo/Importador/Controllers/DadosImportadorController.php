<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Domain\Modulos\Importador\Services\DadosImportadorService;
use App\Models\ModuloImportador;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

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
            return back()
                ->withErrors([
                    'arquivo' => 'Já existem dados importados. Exclua os dados atuais antes de importar novamente.',
                ])
                ->with('message', [
                    'type' => 'warning',
                    'content' => 'Já existem dados importados. Exclua os dados atuais antes de importar novamente.',
                ]);
        }

        try {
            $this->service->importarPlanilha($importador, $request->file('arquivo'));

            return back()->with('message', [
                'type' => 'success',
                'content' => 'Planilha importada com sucesso!',
            ]);
        } catch (Throwable $e) {
            Log::error('Erro ao importar planilha do módulo importador', [
                'modulo_importador_id' => $importador->id,
                'arquivo' => $request->file('arquivo')?->getClientOriginalName(),
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $importador->forceFill([
                'load' => false,
                'desc_erros' => [
                    'Não foi possível importar a planilha. Verifique se o arquivo está no modelo correto e tente novamente.',
                    $e->getMessage(),
                ],
            ])->save();

            return back()
                ->withErrors([
                    'arquivo' => 'Não foi possível importar a planilha. Verifique se o arquivo está no modelo correto e tente novamente.',
                ])
                ->with('message', [
                    'type' => 'error',
                    'content' => 'Erro ao importar planilha. Verifique o arquivo enviado.',
                ]);
        }
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
