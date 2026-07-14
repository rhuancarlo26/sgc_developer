<?php

namespace App\Domain\Sgc\Contratada\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLayerRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string',
            'file' => 'required|mimes:zip|max:50000',
        ];
    }
}
