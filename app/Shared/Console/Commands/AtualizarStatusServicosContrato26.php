<?php

namespace App\Shared\Console\Commands;

use App\Models\Servicos;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AtualizarStatusServicosContrato26 extends Command
{
    // comando para rodar => servicos:atualizar-status-contrato
    protected $signature = 'servicos:atualizar-status-contrato
                            {contratoId=26 : ID do contrato}
                            {status=1 : Novo status_aprovacao}';

    protected $description = 'Atualiza o campo status_aprovacao dos serviços vinculados a um contrato';

    public function handle(): int
    {
        $contratoId = (int) $this->argument('contratoId');
        $status = (int) $this->argument('status');

        $query = Servicos::query()
            ->where('id_contrato', $contratoId);

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->warn("Nenhum serviço encontrado para id_contrato = {$contratoId}.");
            return self::SUCCESS;
        }

        $this->info("Foram encontrados {$total} serviço(s) com id_contrato = {$contratoId}.");
        $this->info("Novo status_aprovacao: {$status}");

        if (!$this->confirm("Deseja realmente atualizar todos esses registros para status_aprovacao = {$status}?")) {
            $this->warn('Operação cancelada.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($query, $status) {
            $query->update([
                'status_aprovacao' => $status,
                'updated_at' => now(),
            ]);
        });

        $this->info("Status atualizado com sucesso para {$total} serviço(s).");

        return self::SUCCESS;
    }
}
