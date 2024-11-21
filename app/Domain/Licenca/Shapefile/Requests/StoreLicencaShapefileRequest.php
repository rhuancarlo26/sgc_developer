<?php

namespace App\Domain\Licenca\Shapefile\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLicencaShapefileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'licenca_id' => 'required|exists:licencas,id',
            'shapefile' => 'required|file|mimes:zip'
        ];
    }

    public function messages(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        return true;
    }
}
