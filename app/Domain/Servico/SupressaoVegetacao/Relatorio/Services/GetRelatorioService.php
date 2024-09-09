<?php

namespace App\Domain\Servico\SupressaoVegetacao\Relatorio\Services;

use App\Domain\Servico\app\Services\ServicoService;
use App\Domain\Servico\Condicionante\Services\ServicoLicencaCondicionanteService;
use App\Domain\Servico\Equipamento\Services\ServicoEquipamentoService;
use App\Domain\Servico\Rh\Services\ServicoRhService;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Services\PatioEstocagemService;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Services\PlanoSupressaoService;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Services\VincularASVService;
use App\Domain\Servico\Veiculo\Services\ServicoVeiculoService;
use App\Models\ResultadoSupressao;
use App\Models\SupressaoRelatorioAnexo;

class GetRelatorioService
{

    public function getRelatorioData(array $request): array
    {
        return [
            'servico'         => ServicoService::getServicos($request['fk_servico'], $request['contrato_id']),
            'resultado'       => ResultadoSupressao::find($request['fk_resultado']),
            'contrato'        => ContratoService::getContratoRelatorio($request['fk_servico']),
            'rh'              => ServicoRhService::getRhServico($request['fk_servico']),
            'veiculos'        => ServicoVeiculoService::getVeiculoServico($request['fk_servico']),
            'equipamento'     => ServicoEquipamentoService::getEquipamentoServico($request['fk_servico']),
            'licenca'         => ServicoLicencaCondicionanteService::getLicencaCondicionanteServico($request['fk_servico']),
            'plano_supressao' => PlanoSupressaoService::getPlanoSupressaoServico($request['fk_servico']),
            'patio_estocagem' => PatioEstocagemService::getPatioEstocagemServico($request['fk_servico']),
            'asvs'            => VincularASVService::getLicencaServico($request['fk_servico']),
            'anexos'          => SupressaoRelatorioAnexo::query()->where('fk_relatorio', $request['fk_servico'])->get(),
        ];
    }
}
