<?php

namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Domain\Factories;

use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Domain\Contracts\SubprodutoCreatorInterface;
use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Domain\Contracts\SubprodutoInterface;

class SubprodutoFactory
{
    private array $creators = [];

    public function registerCreator(string $tipo, SubprodutoCreatorInterface $creators): void
    {
        $this->creators[$tipo] = $creators;
    }

    public function create(string $tipo, array $data): SubprodutoInterface
    {
        if (!isset($this->creators[$tipo])) {
            throw new \InvalidArgumentException("Tipo {$tipo} não suportado");
        }

        return $this->creators[$tipo]->create($data);
    }
}
