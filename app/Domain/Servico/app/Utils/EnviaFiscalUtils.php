<?php

namespace App\Domain\Servico\app\Utils;

use App\Shared\Utils\DataManagement;

class EnviaFiscalUtils
{
    public function __construct(private readonly DataManagement $dataManagement)
    {
    }

    public function update(array $post, string $modelClass): array
    {
        return $this->dataManagement->update(entity: $modelClass, infos: $post, id: $post['id']);
    }

    public function store(array $post, string $modelClass): array
    {
        return $this->dataManagement->create(entity: $modelClass, infos: $post);
    }

}
