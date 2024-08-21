<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Services;

use App\Domain\Licenca\app\Services\LicencaService;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Services\PlanoSupressaoService;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Services\DestinacaoService;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Services\PilhasService;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\CorteEspecieService;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\SupressaoService;
use App\Models\Destinacao;
use App\Models\Licenca;
use App\Models\ResultadoSupressao;
use App\Models\Servicos;

class AnaliseService
{
    public function __construct(
        private readonly SupressaoService $supressaoService,
        private readonly CorteEspecieService $corteEspecieService,
        private readonly PlanoSupressaoService $planoSupressaoService,
        private readonly LicencaService $licencaService,
        private readonly PilhasService $pilhasService,
        private readonly DestinacaoService $destinacaoService,
    )
    {
    }

    public function getResultadoAnaliseSupressao(Servicos $servico, ResultadoSupressao $resultado): array
    {
        $supressao = $this->supressaoService->getByPeriodo($servico, $resultado->dt_inicio, $resultado->dt_final, ['bioma:id,nome', 'licenca:id,numero_licenca', 'estagioSucessional:id,nome']);
        $cortes = $this->corteEspecieService->getSomaByAreaSupressao($supressao->pluck('id')->toArray());
        $supressaoArea = $this->supressaoService->getSumAreaByServico($servico, $resultado->dt_inicio, $resultado->dt_final);
        $planoSupressaoArea = $this->planoSupressaoService->getSumAreaByServico($servico->id);

        $licencas = Licenca::whereIn('id', $supressao->pluck('licenca_id')->toArray())->get();

        $licencaArea = $licencas->reduce(function (array $carry, Licenca $item) {
            $carry['area_app'] += isset($item['in_app']) ? 0 : $item['in_app'];
            $carry['area_fora'] += $item['out_app'];
            return $carry;
        }, ['area_app' => 0, 'area_fora' => 0]);

        $areaAppLicenca = $licencaArea['area_app'];
        $areaForaAppLicenca = $licencaArea['area_fora'];
        $areaTotalLicenca = $areaAppLicenca + $areaForaAppLicenca;

        $areaTotalSupressao = $supressaoArea->area_em_app + $supressaoArea->area_fora_app;

        $resumo = [
            ['desc' => 'Plano de supressão', 'area_app' => $planoSupressaoArea->area_em_app, 'area_fora' => $planoSupressaoArea->area_fora_app, 'total' => $planoSupressaoArea->area_em_app + $planoSupressaoArea->area_fora_app],
            ['desc' => 'Supressão', 'area_app' => $supressaoArea->area_em_app, 'area_fora' => $supressaoArea->area_fora_app, 'total' => $areaTotalSupressao],
            ['desc' => 'ASV', 'area_app' => $areaAppLicenca, 'area_fora' => $areaForaAppLicenca, 'total' => $areaTotalLicenca]
        ];

        $percentualLicenca = $areaTotalLicenca === 0 ? 0 : ($areaTotalSupressao / $areaTotalLicenca) * 100;
        $percentualSuprimido = number_format($percentualLicenca, 2, ',', ' ');

        $areaMes = $this->supressaoService->getSumAreaPerMonthByServico($servico, $resultado->dt_inicio, $resultado->dt_final);

        return compact('supressao', 'cortes', 'resumo', 'percentualSuprimido', 'areaMes');
    }

    public function getResultadoAnalisePilha(Servicos $servico, ResultadoSupressao $resultado): array
    {
        $pilhas = $this->pilhasService->getByPeriodo($servico, $resultado->dt_inicio, $resultado->dt_final);
        $pilha = $pilhas['pilhas'];
        $pilhaProtegida = $pilhas['pilhasProtegidas'];

        $result = $pilha->reduce(function (array $carry,  $item) {
            if ($item->tipo_produto_id == 1) $carry['organico'] += $item->volume;
            if ($item->tipo_produto_id == 2) $carry['lenha'] += $item->volume;
            if ($item->tipo_produto_id == 3) $carry['comercial'] += $item->volume;
            return $carry;
        }, ['organico' => 0, 'lenha' => 0, 'comercial' => 0]);

        $volumeTotalASV = $this->licencaService->getSumTotalASV($pilha->pluck('licenca_id')->toArray())->volume ?? 0;

        $volume = 0;
        $volumeTotalSuprimido = $result['lenha'] + $result['comercial'];
        if ($volumeTotalASV) {
            $volume = number_format(($volumeTotalSuprimido / $volumeTotalASV) * 100, 2, ',', ' ');
        }

        $resumo = [
            ['desc' => 'Pilhas', 'organico' => $result['organico'], 'lenha' => $result['lenha'], 'comercial' => $result['comercial'], 'total' => $volumeTotalSuprimido],
            ['desc' => 'ASV', 'organico' => '-', 'lenha' => '-', 'comercial' => '-', 'total' => $volumeTotalASV]
        ];

        return compact('pilha', 'pilhaProtegida', 'resumo', 'volume');
    }

    public function getResultadoAnaliseDestinacao(Servicos $servico, ResultadoSupressao $resultado): array
    {
        $destinacao = $this->destinacaoService->getByPeriodo($servico, $resultado->dt_inicio, $resultado->dt_final);

        $volumeDestinado = $destinacao->reduce(function (int $carry, Destinacao $destinacao) {
            return $carry + $destinacao->volume;
        }, 0);

        $volumeEstocado = $this->pilhasService->getTotalEstocado($servico)?->volume ?? 0;
        $percentTotalDestinado = 0;
        if ($volumeDestinado) {
            $percentTotalDestinado = number_format(($volumeDestinado / $volumeEstocado) * 100, 2, ',', ' ');
        }

        return compact('destinacao', 'volumeDestinado', 'volumeEstocado', 'percentTotalDestinado');
    }
}
