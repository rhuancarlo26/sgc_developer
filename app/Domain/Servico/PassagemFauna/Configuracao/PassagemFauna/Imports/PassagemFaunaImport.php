<?php

namespace App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PassagemFaunaImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        return $collection;
    }

    /**
     * Regras de validação para a planilha importada.
     * @return array
     */
    public function rules(): array
    {
        return [
            'rodovia' => ['required'],
            'uf' => ['required'],
            'km' => ['required'],
            'tipo_de_estrutura' => ['required'],
            'classificacao' => ['required'],
            'nome' => ['required'],
            'dimensoes' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'observacao' => ['required']
        ];
    }
}
