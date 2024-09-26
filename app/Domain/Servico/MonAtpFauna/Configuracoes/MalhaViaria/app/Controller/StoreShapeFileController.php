<?php

namespace App\Domain\Servico\MonAtpFauna\Configuracoes\MalhaViaria\app\Controller;

use App\Domain\Servico\MonAtpFauna\Configuracoes\MalhaViaria\app\Requests\StoreShapeFileRequest;
use App\Domain\Servico\MonAtpFauna\Configuracoes\MalhaViaria\app\Services\MalhaViariaService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreShapeFileController extends Controller
{
    public function __construct(private readonly MalhaViariaService $malhaViariaService)
    {
    }

    public function __invoke(StoreShapeFileRequest $request): RedirectResponse
    {
        $this->malhaViariaService->storeShapefile(request: $request->all());

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => 'Arquivo salvo',
        ]);
    }
}
