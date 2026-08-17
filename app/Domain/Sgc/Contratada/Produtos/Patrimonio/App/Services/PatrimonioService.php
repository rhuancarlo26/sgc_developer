<?php

namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Services;

use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Domain\Contracts\SubprodutoInterface;
use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Domain\Factories\SubprodutoFactory;
use App\Domain\Sgc\Contratada\Produtos\Patrimonio\Paipa\App\Services\PaipaCreator;
use Illuminate\Support\Facades\DB;

class PatrimonioService
{
    private SubprodutoFactory $factory;

    public function __construct(SubprodutoFactory $factory, PaipaCreator $paipaCreator)
    {
        $this->factory = $factory;
        $this->factory->registerCreator('paipa', $paipaCreator);
    }

    // private function registerCreators(): void
    // {
    //     // Registrar os criadores de cada tipo
    //     $this->factory->registerCreator('paipa', new PaipaCreator());
    //     $this->factory->registerCreator('raipa', new RaipaCreator());
    // }


    public function createSubProdutos(string $tipo, array $data): SubprodutoInterface
    {
        return DB::transaction(function () use ($tipo, $data) {
            $this->validateCommonRules($data);

            $subProduto = $this->factory->create($tipo, $data);

            $this->afterCreate($subProduto);

            return $subProduto;
        });
    }

    private function validateCommonRules(array $data) {}

    private function afterCreate(SubprodutoInterface $subProduto) {}
}
