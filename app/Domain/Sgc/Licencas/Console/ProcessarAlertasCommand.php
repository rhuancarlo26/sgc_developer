<?php

namespace App\Domain\Sgc\Licencas\Console;

use App\Domain\Sgc\Licencas\Services\AlertaService;
use Illuminate\Console\Command;

class ProcessarAlertasCommand extends Command
{
    protected $signature = 'sgc-licencas:alertas {--simular : Apenas calcula os alertas, sem enviar e-mails nem registrar envio}';

    protected $description = 'Envia por e-mail os alertas de prazos de licenças e autorizações ambientais';

    public function handle(AlertaService $alertaService): int
    {
        $resultado = $alertaService->processar((bool) $this->option('simular'));

        $this->info(sprintf(
            '%s%d prazo(s) em alerta para %d destinatário(s).',
            $this->option('simular') ? '[simulação] ' : '',
            $resultado['prazos'],
            $resultado['destinatarios']
        ));

        if ($resultado['sem_destinatario']) {
            $this->warn("{$resultado['sem_destinatario']} prazo(s) sem responsável nem e-mail padrão (SGC_LICENCAS_EMAILS_PADRAO).");
        }

        return self::SUCCESS;
    }
}
