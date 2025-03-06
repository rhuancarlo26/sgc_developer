<?php

namespace App\Domain\Servico\ContOcorrencia\Resultado\Controller;

use App\Domain\Servico\ContOcorrencia\Resultado\Requests\StoreRequest;
use App\Domain\Servico\ContOcorrencia\Resultado\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, StoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $post = array_merge(
            $validated,
            $this->getDatas($validated)
        );

        $post['id_servico'] = $servico->id;

        $response = $this->resultadoService->store($post);

        return to_route('contratos.contratada.servicos.cont_ocorrencia.resultado.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
    }

    private function getDatas($data)
    {
        $dados = [];

        switch ($data['periodo']) {
            case 'M':
                $d = $data['mes'] . '-01';
                $dados['dt_inicio'] = $d;
                $dados['dt_final'] = date("Y-m-t", strtotime($d));
                break;

            case 'S':
                $isFirstSemester = $data['semestre'] == '1';
                $dados['dt_inicio'] = date($data['ano'] . ($isFirstSemester ? '-01-01' : '-07-01'));
                $dados['dt_final'] = date($data['ano'] . ($isFirstSemester ? '-06-30' : '-12-31'));
                break;

            case 'A':
                $dados['dt_inicio'] = date($data['ano'] . '-01-01');
                $dados['dt_final'] = date($data['ano'] . '-12-31');
                break;

            case 'P':
                $dados['dt_inicio'] = date($data['dt_inicio']);
                $dados['dt_final'] = date($data['dt_final']);
                break;
        }

        return $dados;
    }
}
