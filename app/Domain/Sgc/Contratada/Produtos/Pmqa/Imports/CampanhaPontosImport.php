<?php

namespace App\Domain\Sgc\Contratada\Produtos\Pmqa\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CampanhaPontosImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $collection)
    {
        return $collection;
    }

    public function rules(): array
    {
        return [
            'nomepontocoleta'   => ['required'],
            'lat_x'             => ['required'],
            'long_y'            => ['required'],
            'classificacao'     => ['required'],
            'classe'            => ['required'],
            'tipoambiente'      => ['required'],
            'uf'                => ['required'],
            'municipio'         => ['required'],
            'baciahidrografica' => ['required'],
            'km_rodovia'        => ['required'],
            'estaca'            => ['required'],
            'observacoes'       => ['required']
        ];
    }
}
