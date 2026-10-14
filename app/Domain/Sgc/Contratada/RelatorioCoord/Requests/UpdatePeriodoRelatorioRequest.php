<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Requests;

class UpdatePeriodoRelatorioRequest extends CreateRelatorioRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('Super Admin') ?? false;
    }

    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['contrato.id']);
        return $rules;
    }
}
