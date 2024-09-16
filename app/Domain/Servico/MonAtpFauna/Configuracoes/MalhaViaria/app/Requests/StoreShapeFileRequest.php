<?php

namespace App\Domain\Servico\MonAtpFauna\Configuracoes\MalhaViaria\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShapeFileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'shapefile' => ['required']
        ];
    }

}
