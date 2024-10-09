<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Service;

use App\Models\AfugentFaunaExecFrenteModel;
use App\Models\AfugentFaunaExecRegistroImagemModel;
use App\Models\AfugentFaunaExecRegistroModel;
use App\Models\AfugentFaunaFormaRegistroModel;
use App\Models\AtFaunaGrupoAmostradoModel;
use App\Models\Uf;
use Illuminate\Support\Facades\DB;

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
            ...$request
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

    public function getUfs()
    {
        return Uf::all();
    }

    public function getRegistros($servico)
    {
        return AfugentFaunaExecRegistroModel::leftJoin('afugent_fauna_exec_frente', 'afugent_fauna_exec_registro.id_frente', '=', 'afugent_fauna_exec_frente.id')
            ->leftJoin('at_fauna_grupo_amostrado', 'afugent_fauna_exec_registro.id_grupo_amostrado', '=', 'at_fauna_grupo_amostrado.id')
            ->leftJoin('estados', 'afugent_fauna_exec_registro.id_estado', '=', 'estados.id')
            ->select(
                'afugent_fauna_exec_registro.*',
                'afugent_fauna_exec_frente.rodovia',
                'estados.uf as uf',
                'at_fauna_grupo_amostrado.nome as nome_grupo',
                DB::raw("DATE_FORMAT(afugent_fauna_exec_registro.data_registro, '%d/%m/%Y') as data_registroF"),
                DB::raw("(SELECT nome FROM fauna_exec_status_conservacao WHERE id = afugent_fauna_exec_registro.id_status_conservacao_federal) as nome_status_conserv_federal"),
                DB::raw("(SELECT sigla FROM fauna_exec_status_conservacao WHERE id = afugent_fauna_exec_registro.id_status_conservacao_federal) as sigla_status_conserv_federal"),
                DB::raw("(SELECT nome FROM fauna_exec_status_conservacao WHERE id = afugent_fauna_exec_registro.id_status_conservacao_iucn) as nome_status_conserv_iucn"),
                DB::raw("(SELECT sigla FROM fauna_exec_status_conservacao WHERE id = afugent_fauna_exec_registro.id_status_conservacao_iucn) as sigla_status_conserv_iucn")
            )
            ->where('afugent_fauna_exec_registro.id_servico', $servico->id)
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

    public function getStatusConservacaoFederal()
    {
        return DB::table('fauna_exec_status_conservacao')
            ->whereIn('id', [1, 7, 9, 10])
            ->get();
    }

    public function getStatusConservacaoIucn()
    {
        return DB::table('fauna_exec_status_conservacao')
            ->whereIn('id', [2, 3, 4, 5, 6, 7, 8, 9, 10])
            ->get();
    }

    public function storeFile($registro, $file)
    {
        $nome = $file->getClientOriginalName();
        $key = uniqid();
        $caminho = $file->storeAs('public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'Afuget_fauna' . DIRECTORY_SEPARATOR . 'Registro' . DIRECTORY_SEPARATOR . 'Img' . DIRECTORY_SEPARATOR . $key . $nome);

        return AfugentFaunaExecRegistroImagemModel::create([
            'chave' => $key,
            'id_registro' => $registro->id,
            'nome' => $nome,
            'caminho_imagem' => str_replace("public\\", "", $caminho)
        ]);
    }
}
