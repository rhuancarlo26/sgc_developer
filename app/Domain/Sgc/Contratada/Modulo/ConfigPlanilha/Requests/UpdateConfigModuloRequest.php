<?php

namespace App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Shared\Traits\ProdutosReservados;

class UpdateConfigModuloRequest extends FormRequest
{
    use ProdutosReservados;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => 'required',
            'planilha_modelo' => 'nullable|mimes:xlsx,csv',
            'campos' => 'required|array',
            'produto_slug' => ['required', 'string', 'max:50', 'alpha_dash', Rule::notIn($this->produtosReservados())],
            'produto_titulo' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => 'O campo é obrigatório',
            'planilha_modelo.mimes' => 'A planilha modelo precisa ter as extensões .xlsx OU .csv',
            'produto_slug.not_in' => 'Esse nome já é usado por um produto do sistema, escolha outro.',
            'produto_slug.required' => 'Selecione o produto ao qual este módulo será vinculado.',
            'produto_titulo.required' => 'Informe o título do produto.',
        ];
    }
}
