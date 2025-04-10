<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Controller;

use App\Domain\Sgc\Contratada\RelatorioCoord\Services\UpdateStatusService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatusUpdateController extends Controller
{
    public function updateStatus(Request $request, UpdateStatusService $service)
    {
        try {
            $service->updateStatus($request->contrato_id, $request->relatorio_num);
            return response()->json(['message' => 'Status atualizado com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function revisaoStatus(Request $request, UpdateStatusService $service)
    {
        try {
            $service->revisaoStatus($request->contrato_id, $request->relatorio_num);
            return response()->json(['message' => 'Enviado para revisão']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function aprovadoStatus(Request $request, UpdateStatusService $service)
    {
        try {
            $service->aprovadoStatus($request->contrato_id, $request->relatorio_num);
            return response()->json(['message' => 'Relatório aprovado']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function consultoriaStatus(Request $request, UpdateStatusService $service)
    {
        try {
            $service->consultoriaStatus($request->contrato_id, $request->relatorio_num);
            return response()->json(['message' => 'Enviado para consultoria técnica']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
