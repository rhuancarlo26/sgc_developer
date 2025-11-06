<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaCampanha;
use App\Models\SgcFaunaCampanhaAbios;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CampanhaService
{
    protected $profissionalService;
    protected $moduloAmostralService;
    protected $pontoService;
    protected $metodologiaService;
    protected $resultadoService;
    protected $anexoService;
    protected $comentarioService;

    public function __construct(
        ProfissionalService $profissionalService,
        ModuloAmostralService $moduloAmostralService,
        PontoService $pontoService,
        MetodologiaService $metodologiaService,
        ResultadoService $resultadoService,
        AnexoService $anexoService,
        ComentarioService $comentarioService
    ) {
        $this->profissionalService = $profissionalService;
        $this->moduloAmostralService = $moduloAmostralService;
        $this->pontoService = $pontoService;
        $this->metodologiaService = $metodologiaService;
        $this->resultadoService = $resultadoService;
        $this->anexoService = $anexoService;
        $this->comentarioService = $comentarioService;
    }

    public function salvarCampanha($contratoId, array $data)
    {
        $campanha = SgcFaunaCampanha::create([
            'id_contrato' => $contratoId,
            'id_campanha' => $data['id_campanha'] ?? null,
            'modulos_amostrais' => null,
            'data_ini' => $data['data_campanha_inicial'] ?? null,
            'data_fim' => $data['data_campanha_final'] ?? null,
            'periodo' => $data['periodo'] ?? null,
            'observacoes' => $data['observacoes'] ?? null,
            'cod_emp' => $data['cod_emp'] ?? null,
            'subproduto' => $data['subproduto'] ?? null,
            'status' => $data['status'] ?? 'Em análise',
            'versao_analise' => 1,
        ]);

        $this->profissionalService->salvarProfissionais($contratoId, $campanha->id, $data['profissionais'] ?? []);
        $this->moduloAmostralService->salvarModulosAmostrais($contratoId, $campanha->id, $data['modulos_amostrais'] ?? []);
        $this->pontoService->salvarPontosQuelonios($contratoId, $campanha->id, $data['pontos_quelo_crocod'] ?? [], $data['nao_se_aplica_quelo'] ?? false);
        $this->pontoService->salvarPontosCavernicola($contratoId, $campanha->id, $data['pontos_cavernicola'] ?? [], $data['nao_se_aplica_cavernicola'] ?? false);
        $this->metodologiaService->salvarMetodologias($contratoId, $campanha->id, $data['metodologias'] ?? []);
        $this->resultadoService->salvarResultados($contratoId, $data['planilha'] ?? null, $campanha->id, $data['consideracoes'] ?? null);
        $this->anexoService->salvarAnexos($contratoId, $campanha->id, $data['anexos'] ?? []);

        return $campanha->id;
    }

    public function atualizarCampanha($contrato, $campanhaId, array $data)
    {
        $campanha = SgcFaunaCampanha::findOrFail($campanhaId);

        $campanha->update([
            'id_contrato' => $contrato,
            'cod_emp' => $data['cod_emp'] ?? $campanha->cod_emp,
            'subproduto' => $data['subproduto'] ?? $campanha->subproduto,
            'data_ini' => $data['data_campanha_inicial'] ?? $campanha->data_ini,
            'data_fim' => $data['data_campanha_final'] ?? $campanha->data_fim,
            'periodo' => $data['periodo'] ?? $campanha->periodo,
            'observacoes' => $data['observacoes'] ?? $campanha->observacoes,
            'status' => $data['status'] ?? 'Em análise',
            'versao_analise' => $campanha->versao_analise ?? 1,
        ]);

        $this->profissionalService->salvarProfissionais($contrato, $campanha->id, $data['profissionais'] ?? []);
        $this->moduloAmostralService->salvarModulosAmostrais($contrato, $campanha->id, $data['modulos_amostrais'] ?? []);
        $this->pontoService->salvarPontosQuelonios($contrato, $campanha->id, $data['pontos_quelo_crocod'] ?? [], $data['nao_se_aplica_quelo'] ?? false);
        $this->pontoService->salvarPontosCavernicola($contrato, $campanha->id, $data['pontos_cavernicola'] ?? [], $data['nao_se_aplica_cavernicola'] ?? false);
        $this->metodologiaService->salvarMetodologias($contrato, $campanha->id, $data['metodologias'] ?? []);
        $this->resultadoService->salvarResultados($contrato, $data['planilha'] ?? null, $campanha->id, $data['consideracoes'] ?? null);
        $this->anexoService->salvarAnexos($contrato, $campanha->id, $data['anexos'] ?? []);

        return $campanha->id;
    }

    public function atualizarParcialCampanha($campanha, $aba, $data)
    {
        switch ($aba) {
            case 'apresentacao':
                $campanha->update([
                    'data_ini' => $data['data_campanha_inicial'] ?? null,
                    'data_fim' => $data['data_campanha_final'] ?? null,
                    'periodo' => $data['periodo'] ?? null,
                    'observacoes' => $data['observacoes'] ?? null,
                    'cod_emp' => $data['cod_emp'] ?? null,
                    'nao_se_aplica_quelo' => $data['nao_se_aplica_quelo'] ?? false,
                    'nao_se_aplica_cavernicola' => $data['nao_se_aplica_cavernicola'] ?? false,
                ]);
                $this->profissionalService->salvarProfissionais($campanha->id_contrato, $campanha->id, $data['profissionais'] ?? []);
                $this->moduloAmostralService->salvarModulosAmostrais($campanha->id_contrato, $campanha->id, $data['modulos_amostrais'] ?? []);
                $this->pontoService->salvarPontosQuelonios($campanha->id_contrato, $campanha->id, $data['pontos_quelo_crocod'] ?? [], $data['nao_se_aplica_quelo'] ?? false);
                $this->pontoService->salvarPontosCavernicola($campanha->id_contrato, $campanha->id, $data['pontos_cavernicola'] ?? [], $data['nao_se_aplica_cavernicola'] ?? false);
                break;
            case 'metodologia':
                $this->metodologiaService->salvarMetodologias($campanha->id_contrato, $campanha->id, $data['metodologias'] ?? []);
                break;
            case 'resultados':
                $this->resultadoService->salvarResultados($campanha->id_contrato, $data['planilha'] ?? null, $campanha->id, $data['consideracoes'] ?? null);
                break;
            case 'anexos':
                $this->anexoService->salvarAnexos($campanha->id_contrato, $campanha->id, $data['anexos'] ?? []);
                break;
        }
    }

    public function getProfissionaisByContrato($contratoId)
    {
        return $this->profissionalService->getProfissionaisByContrato($contratoId);
    }

    public function getComentariosByCampanha($contratoId, $campanhaId)
    {
        return $this->comentarioService->getComentariosByCampanha($contratoId, $campanhaId);
    }
}