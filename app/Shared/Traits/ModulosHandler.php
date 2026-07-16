<?php

namespace App\Shared\Traits;

trait ModulosHandler
{
    public function buscarParams(): array
    {
        return [
            ['label' => 'Texto', 'value' => 'texto', 'tipoInput' => 'text'],
            ['label' => 'Inteiro', 'value' => 'inteiro', 'tipoInput' => 'number'],
            ['label' => 'Decimal', 'value' => 'decimal', 'tipoInput' => 'number'],
            ['label' => 'Data', 'value' => 'data', 'tipoInput' => 'date'],
        ];
    }
}
