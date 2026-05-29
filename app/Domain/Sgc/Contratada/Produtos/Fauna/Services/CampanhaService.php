<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaCampanha;
use App\Models\SgcFaunaCampanhaAbios;
use App\Models\SgcFaunaAtropelamentoCampanha;
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
        if (!empty($data['planilha_terrestre'])) {
            $this->resultadoService->salvarResultados(
                $contratoId,
                $data['planilha_terrestre'],
                $campanha->id,
                $data['consideracoes'] ?? null,
                'terrestre'
            );
        }

        if (!empty($data['planilha_aquatica'])) {
            $this->resultadoService->salvarResultados(
                $contratoId,
                $data['planilha_aquatica'],
                $campanha->id,
                $data['consideracoes'] ?? null,
                'aquatica'
            );
        }

        if (!empty($data['planilha_cavernicola'])) {
            $this->resultadoService->salvarResultados(
                $contratoId,
                $data['planilha_cavernicola'],
                $campanha->id,
                $data['consideracoes'] ?? null,
                'cavernicola'
            );
        }

        // Salvar campanhas de atropelamento
        if (!empty($data['atropelamento_campanha'])) {
            foreach ($data['atropelamento_campanha'] as $campData) {
                SgcFaunaAtropelamentoCampanha::create([
                    'campanha_id' => $campanha->id,
                    'id_contrato' => $contratoId,
                    'rodovia' => $campData['rodovia'] ?? null,
                    'data_inicial' => $campData['data_inicial'] ?? null,
                    'data_final' => $campData['data_final'] ?? null,
                    'uf_inicial' => $campData['uf_inicial'] ?? null,
                    'uf_final' => $campData['uf_final'] ?? null,
                    'km_inicial' => $campData['km_inicial'] ?? null,
                    'km_final' => $campData['km_final'] ?? null,
                    'latitude_inicial' => $campData['latitude_inicial'] ?? null,
                    'longitude_inicial' => $campData['longitude_inicial'] ?? null,
                    'latitude_final' => $campData['latitude_final'] ?? null,
                    'longitude_final' => $campData['longitude_final'] ?? null,
                    'obs' => $campData['obs'] ?? null,
                ]);
            }
        }

        // Salvar planilha de resultados de atropelamento
        $planilhaPath = null;
        if (!empty($data['planilha_atropelamento']) && $data['planilha_atropelamento']->isValid()) {
            $file = $data['planilha_atropelamento'];
            $filename = time() . '_' . $file->getClientOriginalName();
            $planilhaPath = $file->storeAs('fauna_atropelamento', $filename, 'public');
        }

        $updateFields = [];
        if ($planilhaPath) {
            $updateFields['planilha_atropelamento'] = $planilhaPath;
        }
        if (isset($data['consideracoes_atropelamento'])) {
            $updateFields['consideracoes_atropelamento'] = $data['consideracoes_atropelamento'];
        }
        if (!empty($updateFields)) {
            $campanha->update($updateFields);
        }

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
        // Atualizar campanhas de atropelamento
        if (isset($data['atropelamento_campanha'])) {
            SgcFaunaAtropelamentoCampanha::where('campanha_id', $campanha->id)->delete();
            foreach ($data['atropelamento_campanha'] as $campData) {
                SgcFaunaAtropelamentoCampanha::create([
                    'campanha_id' => $campanha->id,
                    'id_contrato' => $contrato,
                    'rodovia' => $campData['rodovia'] ?? null,
                    'data_inicial' => $campData['data_inicial'] ?? null,
                    'data_final' => $campData['data_final'] ?? null,
                    'uf_inicial' => $campData['uf_inicial'] ?? null,
                    'uf_final' => $campData['uf_final'] ?? null,
                    'km_inicial' => $campData['km_inicial'] ?? null,
                    'km_final' => $campData['km_final'] ?? null,
                    'latitude_inicial' => $campData['latitude_inicial'] ?? null,
                    'longitude_inicial' => $campData['longitude_inicial'] ?? null,
                    'latitude_final' => $campData['latitude_final'] ?? null,
                    'longitude_final' => $campData['longitude_final'] ?? null,
                    'obs' => $campData['obs'] ?? null,
                ]);
            }
        }

        // Atualizar planilha de resultados de atropelamento
        $updateFields = [];
        if (!empty($data['planilha_atropelamento']) && $data['planilha_atropelamento']->isValid()) {
            $file = $data['planilha_atropelamento'];
            $filename = time() . '_' . $file->getClientOriginalName();
            if ($campanha->planilha_atropelamento) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($campanha->planilha_atropelamento);
            }
            $updateFields['planilha_atropelamento'] = $file->storeAs('fauna_atropelamento', $filename, 'public');
        }
        if (array_key_exists('consideracoes_atropelamento', $data)) {
            $updateFields['consideracoes_atropelamento'] = $data['consideracoes_atropelamento'];
        }
        if (!empty($updateFields)) {
            $campanha->update($updateFields);
        }

        // $this->resultadoService->salvarResultados($contrato, $data['planilha'] ?? null, $campanha->id, $data['consideracoes'] ?? null);
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

    // --------------------------------------------------------------------------
    // Método 1: salvarSubDadosApresentacao
    // Usado pelo SalvarEtapaCampanhaController na etapa 'apresentacao'.
    // Chama os sub-serviços exatamente como o salvarCampanha já faz.
    // --------------------------------------------------------------------------

    public function salvarSubDadosApresentacao($contratoId, $campanhaId, array $data): void
    {
        $this->profissionalService->salvarProfissionais(
            $contratoId,
            $campanhaId,
            $data['profissionais'] ?? []
        );

        $this->moduloAmostralService->salvarModulosAmostrais(
            $contratoId,
            $campanhaId,
            $data['modulos_amostrais'] ?? []
        );

        $this->pontoService->salvarPontosQuelonios(
            $contratoId,
            $campanhaId,
            $data['pontos_quelo_crocod'] ?? [],
            $data['nao_se_aplica_quelo'] ?? false
        );

        $this->pontoService->salvarPontosCavernicola(
            $contratoId,
            $campanhaId,
            $data['pontos_cavernicola'] ?? [],
            $data['nao_se_aplica_cavernicola'] ?? false
        );
    }

    // --------------------------------------------------------------------------
    // Método 2: salvarMetodologias (wrapper público para o SalvarEtapaController)
    // --------------------------------------------------------------------------

    public function salvarMetodologias($contratoId, $campanhaId, array $metodologias): void
    {
        $this->metodologiaService->salvarMetodologias($contratoId, $campanhaId, $metodologias);
    }

    // --------------------------------------------------------------------------
    // Método 3: salvarResultadosPorTipo (wrapper público)
    // --------------------------------------------------------------------------

    public function salvarResultadosPorTipo($contratoId, $campanhaId, $file, ?string $consideracoes, string $tipo): void
    {
        $this->resultadoService->salvarResultados($contratoId, $file, $campanhaId, $consideracoes, $tipo);
    }

    // --------------------------------------------------------------------------
    // Método 4: salvarAnexos (wrapper público)
    // --------------------------------------------------------------------------

    public function salvarAnexos($contratoId, $campanhaId, array $anexos): void
    {
        $this->anexoService->salvarAnexos($contratoId, $campanhaId, $anexos);
    }

    // --------------------------------------------------------------------------
    // Método 5: getRascunhoAberto
    // Usado pelo ProdutosController para pré-carregar rascunho na tela de Create
    // --------------------------------------------------------------------------

    public function getRascunhoAberto($contratoId, string $subproduto, string $codEmp): ?array
    {
        $campanha = \App\Models\SgcFaunaCampanha::where('id_contrato', $contratoId)
            ->where('subproduto', $subproduto)
            ->where('cod_emp', $codEmp)
            ->where('status', 'rascunho')
            ->with([
                'abios.abio',
                'profissionais.profissional',
                'modulos_amostrais',
                'pontos_quelo_crocod',
                'pontos_cavernicola',
                'metodologias',
                'resultadosTerrestre',
                'resultadosAquatica',
                'resultadosCavernicola',
                'resultados_consideracoes',
                'anexos',
            ])
            ->latest()
            ->first();

        if (!$campanha) {
            return null;
        }

        return [
            'campanha_id' => $campanha->id,
            'etapa_atual' => $campanha->etapa_atual ?? 'apresentacao',
            'dados'       => \App\Domain\Sgc\Contratada\Produtos\Fauna\Resources\CampanhaResource::toArray($campanha),
        ];
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