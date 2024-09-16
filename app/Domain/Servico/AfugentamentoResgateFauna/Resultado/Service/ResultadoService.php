<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Service;

use App\Models\AfugentFaunaResultadoModel;

class ResultadoService
{
    public function __construct(private readonly AfugentFaunaResultadoModel $afugentFaunaResultadoModel) {}

    public function create($request, $servico)
    {
        $datas = $this->getDatas($request);

        return $this->afugentFaunaResultadoModel->create([
            'chave' => md5(uniqid()),
            'id_servico' => $servico->id,
            'nome' => $request['nome'],
            'periodo' => $request['periodo'],
            'dt_inicio' => $datas['dt_inicio'],
            'dt_final' => $datas['dt_final'],
        ]);
    }

    public function update($request, $resultado)
    {
        $datas = $this->getDatas($request);

        $resultado->update([
            'nome' => $request['nome'],
            'periodo' => $request['periodo'],
            'dt_inicio' => $datas['dt_inicio'],
            'dt_final' => $datas['dt_final'],
        ]);
    }

    public function delete($resultado)
    {
        $resultado->delete();
    }

    public function getDatas($request)
    {
        /* convertendo as datas de acordo com o periodo selecionado  */
        if ($request['periodo'] == 'M') {
            $d = $request['mes'] . '-01';
            $dados['dt_inicio'] = $d;
            $dados['dt_final'] = date("Y-m-t", strtotime($d));
        } else if ($request['periodo'] == 'S') {
            if ($request['semestre'] == '1') {
                $dados['dt_inicio'] = date($request['ano'] . '-01-01');
                $dados['dt_final'] = date($request['ano'] . '-06-30');
            } else {
                $dados['dt_inicio'] = date($request['ano'] . '-07-01');
                $dados['dt_final'] = date($request['ano'] . '-12-31');
            }
        }
        if ($request['periodo'] == 'A') {
            $dados['dt_inicio'] = date($request['ano'] . '-01-01');
            $dados['dt_final'] = date($request['ano'] . '-12-31');
        }
        if ($request['periodo'] == 'P') {
            $dados['dt_inicio'] = date($request['dt_inicio']);
            $dados['dt_final'] = date($request['dt_final']);
        }
        return $dados;
    }

    public function getResultado($servico)
    {
        return $this->afugentFaunaResultadoModel->where('id_servico', $servico->id)->paginate(10);
    }

    public function getResultadoAnalise($resultado)
    {
        $dados['registros_identificados'] = $this->afugentFaunaResultadoModel->getRegistrosIdentificados($resultado->id, $resultado->dt_inicio, $resultado->dt_final);
        $dados['registros_classe'] = $this->afugentFaunaResultadoModel->getRegistrosClasse($resultado->id, $resultado->dt_inicio, $resultado->dt_final);
        $dados['registros_periodo'] = $this->afugentFaunaResultadoModel->getRegistrosPeriodo($resultado->id, $resultado->dt_inicio, $resultado->dt_final);
        $dados['formas_registros'] = $this->afugentFaunaResultadoModel->getFormasRegistros($resultado->id, $resultado->dt_inicio, $resultado->dt_final);
        $dados['registros_km'] = $this->afugentFaunaResultadoModel->getRegistrosKm($resultado->id, $resultado->dt_inicio, $resultado->dt_final);
        $dados['registros_mes'] = $this->afugentFaunaResultadoModel->getRegistrosMes($resultado->id, $resultado->dt_inicio, $resultado->dt_final);
        $dados['registros_especie'] = $this->afugentFaunaResultadoModel->getRegistrosEspecie($resultado->id, $resultado->dt_inicio, $resultado->dt_final);
        
        return response()->json($dados);
    }
}
