<?php

namespace App\Domain\Sgc\Licencas\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlertaPrazosMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array $prazos Resumos de prazo (PainelService::resumoPrazo)
     */
    public function __construct(public readonly array $prazos)
    {
    }

    public function envelope(): Envelope
    {
        $vencidos = collect($this->prazos)->where('dias_restantes', '<', 0)->count();

        return new Envelope(
            subject: 'SGC - Prazos de licenças: ' . count($this->prazos) . ' alerta(s)' . ($vencidos ? " ({$vencidos} vencido(s))" : ''),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.sgc_licencas.alerta');
    }
}
