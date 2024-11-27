<?php

namespace App\Domain\Sgc\Contratada\app\Controller;

use App\Domain\Sgc\Contratada\app\Services\EmpreendimentosService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UpdateEmpreendimentoController extends Controller
{
    public function __construct(
        private readonly EmpreendimentosService $empreendimentoService)
    {
    }

    public function updateEmpreendimento(Request $request)
    {
        $id = $request->id;
        
        // Tenta localizar o empreendimento pelo ID
        $empreendimento = $this->empreendimentoService->buscarEmpreendimentoPorId($id);
        if ($empreendimento) {
            // Atualiza o empreendimento existente
            $this->empreendimentoService->atualizarEmpreendimento($empreendimento->id, $request->all());
            return redirect()->back()->with('success', 'Empreendimento atualizado com sucesso.');
        } else {
            // Cria um novo empreendimento
            $this->empreendimentoService->salvarEmpreendimento($request->all());
            return redirect()->back()->with('success', 'Novo empreendimento criado com sucesso.');
        }
    }
    
}