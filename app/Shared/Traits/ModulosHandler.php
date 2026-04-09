<?php

namespace App\Shared\Traits;

trait ModulosHandler
{
    public function buscarParams(): array
    {
        return [
            ['label' => 'Texto', 'value' => 'texto'],
            ['label' => 'Inteiro', 'value' => 'inteiro'],
            ['label' => 'Decimal', 'value' => 'decimal'],
            ['label' => 'Data', 'value' => 'data'],
        ];
    }
}
