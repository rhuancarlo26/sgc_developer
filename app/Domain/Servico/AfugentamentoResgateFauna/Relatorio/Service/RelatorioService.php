<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Service;

use App\Models\AfugentFaunaRelatorioAnexosModel;
use App\Models\AfugentFaunaRelatorioModel;

class RelatorioService
{
    public function __construct(private readonly AfugentFaunaRelatorioModel $afugentFaunaRelatorioModel) {}

    public function getRelatorio($servico)
    {
        return $this->afugentFaunaRelatorioModel->getRelatorios($servico->id);
    }

    public function create($request, $servico)
    {
        $this->afugentFaunaRelatorioModel->create([
            'chave' => md5(uniqid()),
            'id_servico' => $servico->id,
            'id_resultado' => $request['id_resultado'],
            'nome_relatorio' => $request['nome'],
            'sobre_relatorio' => $request['sobre_relatorio'],
            'fk_status' => 1,
        ]);
    }

    public function update($request, $relatorio)
    {
        $relatorio->update([
            'id_resultado' => $request['id_resultado'],
            'nome_relatorio' => $request['nome'],
            'sobre_relatorio' => $request['sobre_relatorio'],
        ]);
    }

    public function destroy($relatorio)
    {
        $relatorio->delete();
    }

    public function updateConclusao($request, $relatorio)
    {
        $relatorio->update([
            'conclusao' => $request['conclusao']
        ]);
    }

    public function createAnexo($relatorio, $arquivo)
    {
        $nome = $arquivo->getClientOriginalName();
        $key = uniqid();
        $path = $arquivo->storeAs('public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'Afuget_fauna' . DIRECTORY_SEPARATOR . 'Relatorio' . DIRECTORY_SEPARATOR . $key . $nome);

        return AfugentFaunaRelatorioAnexosModel::create([
            'chave' => $key,
            'id_relatorio' => $relatorio->id,
            'nome_arquivo' => $nome,
            'caminho_arquivo' => $path,
        ]);
    }

    public function destroyAnexo($anexo)
    {
        $anexo->delete();
    }

    public function getAnexos($relatorio)
    {
        return AfugentFaunaRelatorioAnexosModel::where('id_relatorio', $relatorio->id)->get();
    }
}
