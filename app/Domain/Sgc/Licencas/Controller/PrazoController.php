<?php

namespace App\Domain\Sgc\Licencas\Controller;

use App\Domain\Sgc\Licencas\Requests\EventoRequest;
use App\Domain\Sgc\Licencas\Requests\RegistroRequest;
use App\Domain\Sgc\Licencas\Services\PrazoService;
use App\Models\SgcLicenca;
use App\Models\SgcLicencaPrazo;
use App\Models\SgcLicencaPrazoRegistro;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrazoController extends Controller
{
    public function __construct(private readonly PrazoService $prazoService)
    {
    }

    public function evento(EventoRequest $request, SgcLicenca $licenca): RedirectResponse
    {
        $condicionante = $licenca->condicionantes()->findOrFail($request->integer('condicionante_id'));
        $this->prazoService->registrarEvento($condicionante, $request->input('data_evento'));

        return back()->with('message', ['type' => 'success', 'content' => 'Evento lançado e prazo gerado.']);
    }

    public function registrar(RegistroRequest $request, SgcLicencaPrazo $prazo): RedirectResponse
    {
        $this->prazoService->adicionarRegistro($prazo, $request->validated(), $request->file('arquivo'), Auth::id());

        return back()->with('message', ['type' => 'success', 'content' => 'Registro incluído.']);
    }

    public function removerRegistro(SgcLicencaPrazoRegistro $registro): RedirectResponse
    {
        $this->prazoService->removerRegistro($registro);

        return back()->with('message', ['type' => 'success', 'content' => 'Registro excluído e situação do prazo recalculada.']);
    }

    public function arquivoRegistro(SgcLicencaPrazoRegistro $registro): StreamedResponse
    {
        abort_unless($registro->arquivo_caminho, 404);

        return Storage::disk(config('sgc_licencas.disco'))->download($registro->arquivo_caminho, $registro->arquivo_nome);
    }
}
