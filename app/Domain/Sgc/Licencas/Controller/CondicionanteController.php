<?php

namespace App\Domain\Sgc\Licencas\Controller;

use App\Domain\Sgc\Licencas\Requests\CondicionanteRequest;
use App\Domain\Sgc\Licencas\Services\PrazoService;
use App\Models\SgcLicenca;
use App\Models\SgcLicencaCondicionante;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class CondicionanteController extends Controller
{
    public function __construct(private readonly PrazoService $prazoService)
    {
    }

    public function store(CondicionanteRequest $request, SgcLicenca $licenca): RedirectResponse
    {
        $licenca->condicionantes()->create($request->dados());
        $this->prazoService->sincronizar($licenca);

        return back()->with('message', ['type' => 'success', 'content' => 'Condicionante cadastrada.']);
    }

    public function update(CondicionanteRequest $request, SgcLicencaCondicionante $condicionante): RedirectResponse
    {
        $condicionante->update($request->dados());
        $this->prazoService->sincronizar($condicionante->licenca);

        return back()->with('message', ['type' => 'success', 'content' => 'Condicionante atualizada.']);
    }

    public function destroy(SgcLicencaCondicionante $condicionante): RedirectResponse
    {
        $condicionante->delete();

        return back()->with('message', ['type' => 'success', 'content' => 'Condicionante excluída.']);
    }
}
