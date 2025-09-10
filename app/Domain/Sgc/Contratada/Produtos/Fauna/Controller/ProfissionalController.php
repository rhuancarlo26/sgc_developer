<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\ProfissionalService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\StoreProfissionalRequest;
use Illuminate\Support\Facades\Log;

class ProfissionalController extends Controller
{
    protected $profissionalService;

    public function __construct(ProfissionalService $profissionalService)
    {
        $this->profissionalService = $profissionalService;
    }

    public function storeProfissional(StoreProfissionalRequest $request, $contrato, $produto)
    {
        try {
            $this->profissionalService->salvarProfissional($contrato, $request->validated());
            return redirect()->back()->with('success', 'Profissional salvo com sucesso!');
        } catch (\Exception $e) {
            Log::error('ProfissionalController: Erro ao salvar profissional', [
                'contrato' => $contrato,
                'produto' => $produto,
                'erro' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao salvar profissional: ' . $e->getMessage()]);
        }
    }
}