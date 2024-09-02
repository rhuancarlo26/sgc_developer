<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Requests\UpdateRequest;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services\CampanhasService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{
    public function __construct(private readonly CampanhasService $campanhasService)
    {
    }

    public function __invoke(UpdateRequest $request): RedirectResponse
    {
        $response = $this->campanhasService->update(request: $request->all());

        return redirect()->back()->with('message', $response['request']);
    }
}
