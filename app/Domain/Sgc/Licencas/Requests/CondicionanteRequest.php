<?php

namespace App\Domain\Sgc\Licencas\Requests;

use App\Domain\Sgc\Licencas\Enums\SituacaoCondicionante;
use App\Domain\Sgc\Licencas\Enums\TipoPrazo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CondicionanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $comDias = [TipoPrazo::DiasAposEmissao->value, TipoPrazo::DiasAntesVencimento->value, TipoPrazo::AposEvento->value];

        return [
            'numero' => ['required', 'string', 'max:20'],
            'grupo' => ['nullable', Rule::in(['geral', 'especifica'])],
            'titulo' => ['nullable', 'string', 'max:255'],
            'descricao' => ['required', 'string'],
            'tipo_prazo' => ['required', Rule::enum(TipoPrazo::class)],
            'prazo_dias' => ['nullable', Rule::requiredIf(in_array($this->input('tipo_prazo'), $comDias)), 'integer', 'min:0', 'max:3650'],
            'data_fixa' => ['nullable', 'required_if:tipo_prazo,' . TipoPrazo::DataFixa->value, 'date'],
            'dia_recorrencia' => ['nullable', 'required_if:tipo_prazo,' . TipoPrazo::Anual->value, 'integer', 'between:1,31'],
            'mes_recorrencia' => ['nullable', 'required_if:tipo_prazo,' . TipoPrazo::Anual->value, 'integer', 'between:1,12'],
            'relatorio_sigla' => ['nullable', 'string', 'max:20'],
            'periodo_ano_anterior' => ['boolean'],
            'situacao' => ['required', Rule::enum(SituacaoCondicionante::class)],
            'situacao_descricao' => ['nullable', 'string'],
            'ordem' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'numero' => 'número',
            'descricao' => 'descrição',
            'tipo_prazo' => 'tipo de prazo',
            'prazo_dias' => 'dias',
            'data_fixa' => 'data',
            'dia_recorrencia' => 'dia',
            'mes_recorrencia' => 'mês',
            'situacao' => 'situação',
        ];
    }

    /**
     * Limpa os campos que não se aplicam ao tipo de prazo escolhido.
     */
    public function dados(): array
    {
        $dados = $this->validated();
        $tipo = TipoPrazo::from($dados['tipo_prazo']);

        if (!in_array($tipo, [TipoPrazo::DiasAposEmissao, TipoPrazo::DiasAntesVencimento, TipoPrazo::AposEvento])) {
            $dados['prazo_dias'] = null;
        }
        if ($tipo !== TipoPrazo::DataFixa) {
            $dados['data_fixa'] = null;
        }
        if ($tipo !== TipoPrazo::Anual) {
            $dados['dia_recorrencia'] = $dados['mes_recorrencia'] = null;
            $dados['periodo_ano_anterior'] = false;
        }
        $dados['relatorio_sigla'] = isset($dados['relatorio_sigla']) ? mb_strtoupper(trim($dados['relatorio_sigla'])) ?: null : null;

        return $dados;
    }
}
