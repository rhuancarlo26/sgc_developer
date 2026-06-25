<?php

namespace App\Shared\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTableStructure extends Command
{
    protected $signature = 'db:table-structure {table}';
    protected $description = 'Mostra a estrutura atual de uma tabela do banco';

    public function handle(): int
    {
        $table = $this->argument('table');

        $columns = DB::select("DESCRIBE {$table}");

        $this->table(
            ['Campo', 'Tipo', 'Nulo', 'Chave', 'Default', 'Extra'],
            collect($columns)->map(fn ($col) => [
                $col->Field,
                $col->Type,
                $col->Null,
                $col->Key,
                $col->Default,
                $col->Extra,
            ])->toArray()
        );

        return self::SUCCESS;
    }
}