<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\FaunaService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class FaunaController extends Controller
{
    protected $faunaService;

    public function __construct(FaunaService $faunaService)
    {
        $this->faunaService = $faunaService;
    }

    public function salvarCampanha(Request $request, $contrato, $produto): RedirectResponse
    {
        Log::info('FaunaController: Recebendo requisição para salvar campanha', [
            'contrato' => $contrato,
            'produto' => $produto,
            'dados' => $request->all(),
        ]);

        $validated = $request->validate([
            'data_campanha_inicial' => 'nullable|date',
            'data_campanha_final' => 'nullable|date',
            'periodo' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
            'id_abio' => 'nullable|integer|exists:fauna_config_abio,id',
            'cod_emp' => 'required|string|max:255',
            'subproduto' => 'required|string|max:255',
            'profissionais' => 'nullable|array',
            'profissionais.*.profissional' => 'required_with:profissionais|string|max:255',
            'profissionais.*.grupo_faunistico' => 'required_with:profissionais|string|in:Avifauna,Herpertofauna,Mastofauna,Ictiofauna,Bentos',
        ]);

        $this->faunaService->salvarCampanha($contrato, $validated);

        return redirect()->route('sgc.contratada.produtos.index', [$contrato, $produto])
            ->with('success', 'Campanha salva com sucesso!');
    }

    public function salvarProfissional(Request $request, $contrato, $produto): RedirectResponse
    {
        Log::info('FaunaController: Salvando profissional', [
            'contrato' => $contrato,
            'produto' => $produto,
            'dados' => $request->all(),
        ]);

        $validated = $request->validate([
            'profissional' => 'required|string|max:255',
            'formacao' => 'required|string|max:255',
        ]);

        $this->faunaService->salvarProfissional($contrato, $validated);

        return redirect()->back()->with('success', 'Profissional cadastrado com sucesso!');
    }
}