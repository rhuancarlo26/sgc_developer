<?php

namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\Paipa\App\Services;

use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Domain\Contracts\SubprodutoInterface;
use App\Models\SgcPatrimonioPaipa;

class PaipaAdapter implements SubprodutoInterface
{
    private SgcPatrimonioPaipa $paipa;

    public function __construct(SgcPatrimonioPaipa $paipa)
    {
        $this->paipa = $paipa;
    }

    public function getId(): int
    {
        return $this->paipa->id;
    }

    public function getNome(): string
    {
        return 'PAIPA - ' . $this->paipa->id;
    }

    public function getTipo(): string
    {
        return 'paipa';
    }

    public function toArray(): array
    {
        return $this->paipa->toArray();
    }

    // Método para acessar o modelo original se necessário
    public function getOriginal(): SgcPatrimonioPaipa
    {
        return $this->paipa;
    }
}
