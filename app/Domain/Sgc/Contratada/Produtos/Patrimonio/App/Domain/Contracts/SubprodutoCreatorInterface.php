<?php

namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Domain\Contracts;

interface SubprodutoCreatorInterface
{
    public function create(array $data): SubprodutoInterface;
}
