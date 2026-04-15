<?php

namespace App\Domain\Modulos\Importador\Jobs;

use App\Models\ModuloImportador;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Rap2hpoutre\FastExcel\FastExcel;

class ProcessarPlanilhaImportadorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ModuloImportador $importador;
    /**
     * Create a new job instance.
     */
    public function __construct(
        private int $importadorId,
        private string $caminhoArquivo
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->importador = ModuloImportador::find($this->importadorId);

        $arquivo = storage_path('app' . DIRECTORY_SEPARATOR . $this->caminhoArquivo);

        (new FastExcel())->import($arquivo, function ($row) {

            // montar as validações
            $dados = $row;

            $this->importador->dados()->create([
                'dados' => $dados
            ]);
        });
    }
}
