<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Service\RegistrosService;
use App\Models\Servicos;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Shared\Http\Controllers\Controller;

class DownloadRegistroController extends Controller
{
    public function __construct(private readonly RegistrosService $registrosService)
    {
    }

    public function index(Servicos $servico)
    {
        $planilhaRegistros = $this->registrosService->getRegistros($servico);
        // Filtrar os dados necessários
        $filteredData = $planilhaRegistros->map(function ($registro) {
            return [
                'ID' => $registro->nome_registro,
                'Frente' => $registro->id_frente,
                'Grupo amostrado' => $registro->nome_grupo,
                'Data registro' => $registro->data_registro,
                'Estado' => $registro->uf,
                'KM' => $registro->rodovia,
                'Latitude' => $registro->latitude,
                'Longitude' => $registro->longitude,
                'Sentido' => $registro->sentido,
                'Margem' => $registro->margem,
                'Classe' => $registro->classe,
                'Ordem' => $registro->ordem,
                'Família' => $registro->familia,
                'Gênero' => $registro->genero,
                'Espécie' => $registro->especie,
                'Nome comum' => $registro->nome_comum,
                'Sexo' => $registro->sexo,
                'Faixa etária' => $registro->faixa_etaria,
                'n_individuos' => $registro->n_individuos,
                'Forma registro' => $registro->formaRegistro->nome,
                'Tipo registro' => $registro->tipoRegistro->nome,
                'Destinação registro' => $registro->destinacaoRegistro->nome ?? '',
                'Latitude soltura' => $registro->latitude_soltura,
                'Longitude soltura' => $registro->longitude_soltura,
                'Zona soltura' => $registro->zona_soltura,
                'Nome local' => $registro->nome_local,
                'Coletado' => $registro->coletado,
                'n_registro_tombamento' => $registro->n_registro_tombamento,
                'nome_status_conserv_federal' => $registro->nome_status_conserv_federal,
                'nome_status_conserv_iucn' => $registro->nome_status_conserv_iucn,
                'Observação' => $registro->obs,
            ];
        });

        // Gerar e fazer o download do arquivo Excel
        return (new FastExcel($filteredData))->download('registros.xlsx');
    }
}