<?php

namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Domain\Contracts;

interface SubprodutoInterface
{
    public function getId(): int;
    public function getNome(): string;
    public function getTipo(): string;
    public function toArray(): array;
}
