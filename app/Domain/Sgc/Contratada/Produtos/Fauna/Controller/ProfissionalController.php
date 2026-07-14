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

            $profissional = $this->profissionalService->salvarProfissional($contrato, $request->validated());

            Log::info('ProfessionalController: Profissional salvo com sucesso', [
                'profissional_id' => $profissional->id,
                'contrato' => $contrato,
                'produto' => $produto,
            ]);

            if ($request->expectsJson() || $request->header('X-Inertia') === 'false') {
                return response()->json([
                    'message' => 'Profissional salvo com sucesso!',
                    'success' => true,
                    'profissional' => $profissional,
                ], 201);
            }

            return redirect()->back()->with('success', 'Profissional salvo com sucesso!');

        } catch (\Exception $e) {
            Log::error('ProfessionalController: Erro ao salvar profissional', [
                'contrato' => $contrato,
                'produto' => $produto,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = 'Erro ao salvar profissional: ' . $e->getMessage();

            if ($request->expectsJson() || $request->header('X-Inertia') === 'false') {
                return response()->json([
                    'message' => $errorMessage,
                    'success' => false,
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => $errorMessage]);
        }
    }
}