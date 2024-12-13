<?php

namespace App\Domain\Servico\MonitoraFauna\Execucao\Registro\Controller;

use App\Domain\Servico\MonitoraFauna\Execucao\Registro\Services\RegistroService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
  public function __construct(private readonly RegistroService $registroService) {}

  public function index(Contrato $contrato, Servicos $servico, Request $request): RedirectResponse
  {
    $post = [
      ...$request->all(),
      'id_servico' => $servico->id,
      'id_modulo' => $request['id_modulo']['id'],
      'id_armadilha' => $request['id_armadilha']['id']
    ];

    $post['nome_id'] = $this->nameIdCustom($request['id_grupo'], $request['id_tipo'], $request['id'], $request['id_armadilha']['nome_id']);
    $response = $this->registroService->update(post: $post);

    return to_route('contratos.contratada.servicos.monitora_fauna.execucao.registro.create', ['contrato' => $contrato->id, 'servico' => $servico->id, 'registro' => $request->id])->with('message', $response['request']);
  }

  private function nameIdCustom($grupo, $tipo, $id_registro, $id_armadilha)
  {
    $grupos = [
      '1' => 'Av',
      '2' => 'Hp',
      '3_1' => 'Mt',
      '3_2' => 'Mv',
      '4' => 'Ic',
      '5' => 'Bt',
    ];

    $key = $grupo == '3' ? "{$grupo}_{$tipo}" : $grupo;
    $name = $grupos[$key] ?? '';

    return $name . $id_registro . '-' . $id_armadilha;
  }
}
