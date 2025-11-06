<?php

namespace App\Domain\Servico\MonitoraFauna\Configuracao\ModuloAmostral\Controller;

use App\Domain\Servico\MonitoraFauna\Configuracao\ModuloAmostral\Requests\StoreRequest;
use App\Domain\Servico\MonitoraFauna\Configuracao\ModuloAmostral\Services\ModuloAmostralService;
use App\Models\Contrato;
use App\Models\ServicoMonitoraFaunaConfigArmadilhaMetodo;
use App\Models\ServicoMonitoraFaunaConfigModuloAmostral;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeleteArmadilhaController extends Controller
{
 public function destroy(Contrato $contrato,Servicos $servico,ServicoMonitoraFaunaConfigArmadilhaMetodo $armadilha) {
    try {
        $armadilha->delete();
        return response()->json([
            'type'    => 'success',
            'content' => 'Armadilha excluída com sucesso.',
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'type'    => 'error',
            'content' => 'Falha ao excluir armadilha: ' . $e->getMessage(),
        ], 500);
    }
}

}
