<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Service;

use App\Models\AfugentFaunaExecFrenteModel;
use App\Models\AfugentFaunaExecRegistroModel;
use App\Models\AfugentFaunaFormaRegistroModel;
use App\Models\AtFaunaGrupoAmostradoModel;

class RegistrosService
{
    public function create($request, $servico): AfugentFaunaExecRegistroModel
    {
        $grupoAmostrado = AtFaunaGrupoAmostradoModel::find($request['id_grupo_amostrado']);
        $nome_registro = $this->getNomeRegistro($grupoAmostrado);
        return AfugentFaunaExecRegistroModel::create([
            ...$request,
            'chave' => md5(uniqid()),
            'id_servico' => $servico->id,
            'nome_registro' => $nome_registro,
        ]);
    }

    public function update($request, $registro)
    {
        $registro->update([
            ...$request,
            'id_servico' => $request['id_servico']['id'],
            'id_frente' => $request['id_frente']['id'],
            'id_grupo_amostrado' => $request['id_grupo_amostrado']['id'],
            'id_estado' => $request['id_estado']['id'],
            'id_forma_registro' => $request['id_forma_registro']['id'],
            'id_tipo_registro' => $request['id_tipo_registro']['id'],
            'id_destinacao_registro' => $request['id_destinacao_registro']['id'],
            'id_status_conservacao_federal' => $request['id_status_conservacao_federal']['id'],
            'id_status_conservacao_iucn' => $request['id_status_conservacao_iucn']['id'],
        ]);
    }

    public function delete(AfugentFaunaExecRegistroModel $registro): void
    {
        $registro->delete();
    }

    public function getGrupoAmostrado()
    {
        return AtFaunaGrupoAmostradoModel::all();
    }

    public function getFrenteSupressao($servico)
    {
        return AfugentFaunaExecFrenteModel::where('id_servico', $servico->id)->get();
    }

    public function getFormaRegistro()
    {
        return AfugentFaunaFormaRegistroModel::all();
    }

    public function getRegistros($servico)
    {
        // estados e base_rodovias está comentado porque não existe a tabela estados no local
        return AfugentFaunaExecRegistroModel::join('at_fauna_grupo_amostrado', 'afugent_fauna_exec_registro.id_grupo_amostrado', '=', 'at_fauna_grupo_amostrado.id')
            // ->join('estados', 'afugent_fauna_exec_registro.id_estado', '=', 'estados.id')
            // ->join('base_rodovias', 'afugent_fauna_exec_registro.id_rodovia', '=', 'base_rodovias.id')
            ->where('afugent_fauna_exec_registro.id_servico', $servico->id)
            ->select('afugent_fauna_exec_registro.*', 'at_fauna_grupo_amostrado.nome as nome_grupo_amostrado')
            // ->select('afugent_fauna_exec_registro.*', 'estados.uf as uf')
            // ->select('afugent_fauna_exec_registro.*', 'base_rodovias.rodovia as rodovia')
            ->paginate(10);
    }

    public function getNomeRegistro($grupoAmostrado)
    {
        $nome_registro = 'AR.';

        // Obtendo a quantidade de registros para o grupo amostrado
        $qntd_reg = AfugentFaunaExecRegistroModel::select('id')
            ->where('id_grupo_amostrado', $grupoAmostrado->id)
            ->get();

        // Calculando o número do nome do registro
        $num_nome_registro = count($qntd_reg) + 1;

        // Adicionando a primeira letra do grupo amostrado
        if ($grupoAmostrado->id == '1') {
            $nome_registro .= 'A';
        } else if ($grupoAmostrado->id == '2') {
            $nome_registro .= 'H';
        } else {
            $nome_registro .= 'M';
        }

        // Adicionando a quantidade de registros + 1 de acordo com o grupo amostrado
        if (strlen($num_nome_registro) == 1) {
            $nome_registro .= '00' . $num_nome_registro;
        } else if (strlen($num_nome_registro) == 2) {
            $nome_registro .= '0' . $num_nome_registro;
        } else {
            $nome_registro .= $num_nome_registro;
        }

        return $nome_registro;
    }
}
