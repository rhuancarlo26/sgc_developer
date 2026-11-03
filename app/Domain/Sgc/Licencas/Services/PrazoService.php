<?php

namespace App\Domain\Sgc\Licencas\Services;

use App\Domain\Sgc\Licencas\Enums\TipoPrazo;
use App\Domain\Sgc\Licencas\Enums\TipoRegistro;
use App\Models\SgcLicenca;
use App\Models\SgcLicencaCondicionante;
use App\Models\SgcLicencaPrazo;
use App\Models\SgcLicencaPrazoRegistro;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PrazoService
{
    public function __construct(private readonly PrazoCalculator $calculator)
    {
    }

    /**
     * Gera/atualiza os prazos automáticos (renovação e condicionantes) de uma licença ativa.
     * Prazos que deixaram de existir são removidos apenas se não tiverem registros.
     * Prazos de eventos são mantidos, pois dependem de lançamento manual.
     */
    public function sincronizar(SgcLicenca $licenca): void
    {
        if ($licenca->status !== 'ativa') {
            return;
        }

        DB::transaction(function () use ($licenca) {
            $licenca->load('condicionantes');

            $emissao = CarbonImmutable::parse($licenca->data_emissao);
            $validade = $licenca->data_validade ? CarbonImmutable::parse($licenca->data_validade) : null;
            $inicioCadeia = $this->inicioCadeia($licenca);
            $hoje = CarbonImmutable::today();

            $esperados = [];

            if ($renovacao = $this->calculator->renovacao($validade, $licenca->antecedencia_renovacao_dias)) {
                $esperados[$this->chave(SgcLicencaPrazo::ORIGEM_RENOVACAO, null, 'unico')] = $renovacao + [
                    'origem' => SgcLicencaPrazo::ORIGEM_RENOVACAO, 'condicionante_id' => null,
                ];
            }

            foreach ($licenca->condicionantes as $condicionante) {
                $ocorrencias = $this->calculator->condicionante(
                    $this->dadosCalculo($condicionante), $emissao, $validade, $hoje, $inicioCadeia
                );

                foreach ($ocorrencias as $ocorrencia) {
                    $chave = $this->chave(SgcLicencaPrazo::ORIGEM_CONDICIONANTE, $condicionante->id, $ocorrencia['referencia']);
                    $esperados[$chave] = $ocorrencia + [
                        'origem' => SgcLicencaPrazo::ORIGEM_CONDICIONANTE, 'condicionante_id' => $condicionante->id,
                    ];
                }
            }

            $existentes = $licenca->prazos()
                ->whereIn('origem', [SgcLicencaPrazo::ORIGEM_RENOVACAO, SgcLicencaPrazo::ORIGEM_CONDICIONANTE])
                ->withCount('registros')
                ->get()
                ->keyBy(fn (SgcLicencaPrazo $p) => $this->chave($p->origem, $p->condicionante_id, $p->referencia));

            foreach ($esperados as $chave => $dados) {
                $prazo = $existentes->get($chave) ?? new SgcLicencaPrazo([
                    'licenca_id' => $licenca->id,
                    'origem' => $dados['origem'],
                    'condicionante_id' => $dados['condicionante_id'],
                    'referencia' => $dados['referencia'],
                    'status' => 'pendente',
                ]);

                $prazo->fill([
                    'descricao' => $dados['descricao'],
                    'ano_referencia' => $dados['ano_referencia'],
                    'periodo_inicio' => $dados['periodo_inicio'],
                    'periodo_fim' => $dados['periodo_fim'],
                    'data_limite_original' => $dados['data_limite'],
                ]);
                $prazo->save();

                $this->recalcularEstado($prazo);
            }

            $existentes
                ->filter(fn (SgcLicencaPrazo $p, string $chave) => !isset($esperados[$chave]) && $p->registros_count === 0)
                ->each->delete();
        });
    }

    /**
     * Após uma retificação/renovação, traz para a nova licença os registros (cumprimentos, prorrogações)
     * já lançados na anterior para a mesma ocorrência (mesma condicionante pelo número e mesma referência),
     * evitando que um RARA já entregue reapareça como pendente na licença retificada.
     */
    public function transferirDaAnterior(SgcLicenca $licenca): void
    {
        if (!$licenca->licenca_anterior_id) {
            return;
        }

        DB::transaction(function () use ($licenca) {
            $antigos = SgcLicencaPrazo::with('condicionante:id,numero')
                ->where('licenca_id', $licenca->licenca_anterior_id)
                ->where('origem', SgcLicencaPrazo::ORIGEM_CONDICIONANTE)
                ->has('registros')
                ->get()
                ->keyBy(fn (SgcLicencaPrazo $p) => $p->condicionante?->numero . '|' . $p->referencia);

            $novos = $licenca->prazos()->with('condicionante:id,numero')
                ->where('origem', SgcLicencaPrazo::ORIGEM_CONDICIONANTE)
                ->get();

            foreach ($novos as $novo) {
                $antigo = $antigos->get($novo->condicionante?->numero . '|' . $novo->referencia);

                if (!$antigo) {
                    continue;
                }

                SgcLicencaPrazoRegistro::where('prazo_id', $antigo->id)->update(['prazo_id' => $novo->id]);
                $antigo->delete();
                $this->recalcularEstado($novo);
            }
        });
    }

    /**
     * Lança a ocorrência de um evento (ex.: acidente ambiental) para uma condicionante "após evento".
     */
    public function registrarEvento(SgcLicencaCondicionante $condicionante, string $dataEvento): SgcLicencaPrazo
    {
        if ($condicionante->tipo_prazo !== TipoPrazo::AposEvento->value || !$condicionante->prazo_dias) {
            throw ValidationException::withMessages([
                'data_evento' => 'A condicionante precisa ser do tipo "Dias após um evento" e ter o número de dias.',
            ]);
        }

        $ocorrencia = $this->calculator->evento($condicionante->rotulo(), CarbonImmutable::parse($dataEvento), $condicionante->prazo_dias);

        $existe = SgcLicencaPrazo::where([
            'condicionante_id' => $condicionante->id,
            'origem' => SgcLicencaPrazo::ORIGEM_EVENTO,
            'referencia' => $ocorrencia['referencia'],
        ])->exists();

        if ($existe) {
            throw ValidationException::withMessages(['data_evento' => 'Já existe um evento lançado nesta data para esta condicionante.']);
        }

        return SgcLicencaPrazo::create([
            'licenca_id' => $condicionante->licenca_id,
            'condicionante_id' => $condicionante->id,
            'origem' => SgcLicencaPrazo::ORIGEM_EVENTO,
            'referencia' => $ocorrencia['referencia'],
            'descricao' => $ocorrencia['descricao'],
            'data_evento' => $dataEvento,
            'data_limite_original' => $ocorrencia['data_limite'],
            'data_limite' => $ocorrencia['data_limite'],
            'status' => 'pendente',
        ]);
    }

    public function adicionarRegistro(SgcLicencaPrazo $prazo, array $dados, ?UploadedFile $arquivo, ?int $userId): SgcLicencaPrazoRegistro
    {
        return DB::transaction(function () use ($prazo, $dados, $arquivo, $userId) {
            $tipo = TipoRegistro::from($dados['tipo']);

            $registro = new SgcLicencaPrazoRegistro([
                'prazo_id' => $prazo->id,
                'tipo' => $tipo->value,
                'data' => $dados['data_registro'],
                'numero_oficio' => $dados['numero_oficio'] ?? null,
                'numero_sei' => $dados['numero_sei'] ?? null,
                'descricao' => $dados['descricao'] ?? null,
                'user_id' => $userId,
            ]);

            if ($tipo === TipoRegistro::Prorrogacao) {
                $registro->data_limite_anterior = $prazo->data_limite;
                $registro->data_limite_nova = $dados['data_limite_nova'];
            }

            if ($arquivo) {
                $registro->arquivo_nome = $arquivo->getClientOriginalName();
                $registro->arquivo_caminho = $arquivo->store("sgc_licencas/{$prazo->licenca_id}/registros", config('sgc_licencas.disco'));
            }

            $registro->save();
            $this->recalcularEstado($prazo);

            return $registro;
        });
    }

    public function removerRegistro(SgcLicencaPrazoRegistro $registro): void
    {
        DB::transaction(function () use ($registro) {
            $prazo = $registro->prazo;

            if ($registro->arquivo_caminho) {
                Storage::disk(config('sgc_licencas.disco'))->delete($registro->arquivo_caminho);
            }

            $registro->delete();
            $this->recalcularEstado($prazo);
        });
    }

    /**
     * O estado do prazo é sempre derivado dos registros: a última prorrogação define a data-limite,
     * uma dispensa encerra o prazo e o primeiro cumprimento define a data de cumprimento.
     */
    public function recalcularEstado(SgcLicencaPrazo $prazo): void
    {
        $registros = $prazo->registros()->reorder()->orderBy('id')->get();

        $prorrogacao = $registros->where('tipo', TipoRegistro::Prorrogacao->value)->last();
        $cumprimento = $registros->where('tipo', TipoRegistro::Cumprimento->value)->sortBy('data')->first();
        $dispensa = $registros->contains('tipo', TipoRegistro::Dispensa->value);

        $prazo->data_limite = $prorrogacao?->data_limite_nova ?? $prazo->data_limite_original;
        $prazo->status = $dispensa ? 'dispensado' : ($cumprimento ? 'cumprido' : 'pendente');
        $prazo->data_cumprimento = !$dispensa ? $cumprimento?->data : null;
        $prazo->save();
    }

    /**
     * Emissão da primeira licença numa cadeia de retificações.
     */
    private function inicioCadeia(SgcLicenca $licenca): CarbonImmutable
    {
        $atual = $licenca;
        $visitados = [];

        while ($atual->motivo_vinculo === 'retificacao' && $atual->licenca_anterior_id && !isset($visitados[$atual->id])) {
            $visitados[$atual->id] = true;
            $anterior = SgcLicenca::withTrashed()->find($atual->licenca_anterior_id);

            if (!$anterior) {
                break;
            }
            $atual = $anterior;
        }

        return CarbonImmutable::parse($atual->data_emissao);
    }

    private function dadosCalculo(SgcLicencaCondicionante $condicionante): array
    {
        return [
            'tipo_prazo' => $condicionante->tipo_prazo,
            'prazo_dias' => $condicionante->prazo_dias,
            'data_fixa' => $condicionante->data_fixa?->format('Y-m-d'),
            'dia_recorrencia' => $condicionante->dia_recorrencia,
            'mes_recorrencia' => $condicionante->mes_recorrencia,
            'periodo_ano_anterior' => $condicionante->periodo_ano_anterior,
            'rotulo' => $condicionante->rotulo(),
        ];
    }

    private function chave(string $origem, ?int $condicionanteId, string $referencia): string
    {
        return "{$origem}|{$condicionanteId}|{$referencia}";
    }
}
