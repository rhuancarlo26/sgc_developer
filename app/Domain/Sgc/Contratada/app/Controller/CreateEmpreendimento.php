<?php

namespace App\Domain\Sgc\Contratada\app\Controller;

use App\Domain\Sgc\Contratada\app\Services\EmpreendimentosService;
use App\Models\ContratoTipo;
use App\Models\Rodovia;
use App\Models\Uf;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CreateEmpreendimento extends Controller
{
    public function __construct(
        private readonly EmpreendimentosService $empreendimentoService)
    {
    }

    public function storeEmpreendimento(Request $request)
    {
        $response = $this->empreendimentoService->salvarEmpreendimento($request->all());
        return redirect()->back()->with('success', $response);
    }
    public function create(ContratoTipo $tipo, $id = null)
    {
        $ufs = Uf::all();
        $rodovias = Rodovia::all();
    
        $empreendimento = $id ? $this->empreendimentoService->buscarEmpreendimentoPorId($id) : null;
    
        return Inertia::render('Sgc/Contratada/Relatorio/Empreendimento/TableEmpreendimento/CreateEmpreendimento', [
            'tipo' => $tipo,
            'ufs' => $ufs,
            'rodovias' => $rodovias,
            'empreendimento' => $empreendimento
        ]);
    }
    
}