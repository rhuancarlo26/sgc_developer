<?php

namespace App\Domain\Sgc\Contratada\Produtos\Espeleologia\Services;

use App\Models\SgcEspeleoCampanha;
use App\Models\SgcEspeleoCampanhaProfissional;
use App\Models\SgcEspeleoJustificativa;
use App\Models\SgcEspeleoMetodologia;
use App\Models\SgcEspeleoResultadoAnexo;
use App\Models\SgcEspeleoProfissional;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EspeleoService
{
    public function getProfissionaisByContrato($contratoId)
    {
        return SgcEspeleoProfissional::where('id_contrato', $contratoId)
            ->get(['id', 'profissional', 'formacao']);
    }

    public function proximoIdCampanha($contratoId): string
    {
        return (string) (((int) SgcEspeleoCampanha::where('id_contrato', $contratoId)->max('id_campanha')) + 1);
    }

    /**
     * Serializa, via lock nomeado do MySQL, qualquer trecho que precise calcular
     * o próximo id_campanha e gravar a campanha — evita que duas requisições
     * concorrentes para o mesmo contrato computem o mesmo número (não há
     * constraint de unicidade no banco para id_contrato+id_campanha).
     */
    public function comLockDeCampanha($contratoId, \Closure $callback)
    {
        $lockName = 'espeleo_id_campanha_contrato_' . $contratoId;
        $obtido = DB::selectOne('SELECT GET_LOCK(?, 10) AS obtido', [$lockName]);

        if (!$obtido || (int) $obtido->obtido !== 1) {
            throw new \RuntimeException('Não foi possível obter o lock para gerar o número da campanha de espeleologia.');
        }

        try {
            return $callback();
        } finally {
            DB::select('SELECT RELEASE_LOCK(?)', [$lockName]);
        }
    }

    public function obterOuCriarRascunho($contratoId, $subproduto)
    {
        return $this->comLockDeCampanha($contratoId, function () use ($contratoId, $subproduto) {
            $campanha = SgcEspeleoCampanha::where('id_contrato', $contratoId)
                ->where('subproduto', $subproduto)
                ->where('status', 'Em elaboração')
                ->first();

            if (!$campanha) {
                $campanha = SgcEspeleoCampanha::create([
                    'id_contrato' => $contratoId,
                    'id_campanha' => $this->proximoIdCampanha($contratoId),
                    'subproduto' => $subproduto,
                    'status' => 'Em elaboração',
                ]);
            }

            return $campanha;
        });
    }

    public function salvarCampanha(array $data, $contratoId, $campanhaId = null)
    {
        return DB::transaction(function () use ($data, $contratoId, $campanhaId) {
            Log::info('Iniciando salvamento de campanha', ['campanhaId' => $campanhaId, 'contratoId' => $contratoId, 'data' => $data]);

            $campanha = $campanhaId
                ? SgcEspeleoCampanha::findOrFail($campanhaId)
                : $this->obterOuCriarRascunho($contratoId, $data['subproduto'] ?? '');

            $campanha->fill($data);
            $campanha->id_contrato = $contratoId;
            $campanha->versao_analise = $campanha->versao_analise ?? 1;
            $campanha->status = 'Em análise';
            $campanha->save();

            // Vincular profissionais
            if (isset($data['profissionais']) && is_array($data['profissionais'])) {
                SgcEspeleoCampanhaProfissional::where('campanha_id', $campanha->id)->delete();

                foreach ($data['profissionais'] as $prof) {
                    Log::info('Tentando vincular profissional', ['prof' => $prof]);
                    SgcEspeleoCampanhaProfissional::create([
                        'campanha_id' => $campanha->id,
                        'id_modulo' => $prof['id_modulo'] ?? null,
                        'id_contrato' => $contratoId,
                        'profissional_id' => $prof['profissional_id'],
                    ]);
                }
            }

            // Vincular justificativas
            if (isset($data['justificativas']) && is_array($data['justificativas'])) {
                SgcEspeleoJustificativa::where('campanha_id', $campanha->id)->delete();

                foreach ($data['justificativas'] as $just) {
                    Log::info('Criando justificativa', ['justificativa' => $just]);
                    $tipo = $just['tipo'] ?? 'complementar';
                    SgcEspeleoJustificativa::create([
                        'campanha_id' => $campanha->id,
                        'codigo_sei' => $just['codigo_sei'] ?? null,
                        'id_contrato' => $contratoId,
                        'titulo' => $just['titulo'] ?? null,
                        'justificativa' => $just['justificativa'] ?? '',
                        'tipo' => $tipo,
                    ]);
                }
            } else {
                // Fallback obsoleto, removido para alinhamento com o novo formato
                Log::warning('Formato de justificativas não encontrado, esperando array "justificativas"');
            }

            // Salvar metodologia se fornecida
            if (isset($data['metodologia']) && !empty($data['metodologia'])) {
                // Remove se já existir uma para esta campanha (para update)
                SgcEspeleoMetodologia::where('campanha_id', $campanha->id)->delete();

                SgcEspeleoMetodologia::create([
                    'campanha_id' => $campanha->id,
                    'id_contrato' => $contratoId,
                    'metodologia' => $data['metodologia'],
                ]);
                Log::info('Metodologia salva', ['campanha_id' => $campanha->id, 'metodologia_length' => strlen($data['metodologia'])]);
            }

            if (isset($data['resultados_anexos']) && is_array($data['resultados_anexos'])) {
                foreach ($data['resultados_anexos'] as $anexoData) {
                    SgcEspeleoResultadoAnexo::updateOrCreate(
                        ['id' => $anexoData['id'] ?? null],
                        [
                            'campanha_id' => $campanha->id,
                            'id_contrato' => $contratoId,
                            'nome_arquivo' => $anexoData['nome_arquivo'],
                            'caminho' => $anexoData['caminho'],
                            'tipo' => 'shapefile',
                        ]
                    );
                }
            }

            Log::info('Campanha salva', ['id' => $campanha->id]);
            return $campanha;
        });
    }
}
