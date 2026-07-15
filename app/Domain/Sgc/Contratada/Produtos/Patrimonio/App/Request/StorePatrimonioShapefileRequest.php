<?php

namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Request;

use Illuminate\Foundation\Http\FormRequest;

class StorePatrimonioShapefileRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'patrimonio_paipa_id' => 'required|integer|exists:sgc_patrimonio_paipa,id',
      'nome_campo'          => 'required|string|max:255',
      'shapefile'           => 'required|file|mimes:zip'
    ];
  }

  public function authorize(): bool { return true; }
}
