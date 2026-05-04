<?php

namespace App\Shared\Utils;

use Illuminate\Support\Facades\Mail;

class MailUtil
{
    /**
     * Envia email genérico
     *
     * @param string|array $emails
     * @param string $subject
     * @param string $content (pode ser HTML)
     * @return void
     */
    public static function sendServicoFiscal($emails, string $subject, $servico): void
    {

        if (empty($emails)) {
            return;
        }

        try {
            Mail::send('emails.servico-fiscal', [
                'servico' => $servico,
            ], function ($message) use ($emails, $subject) {
                $message->to($emails)
                    ->subject($subject);
            });
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email para fiscal', [
                'emails' => $emails,
                'subject' => $subject,
                'servico_id' => $servico->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public static function sendParecerFiscal($emails, string $subject, $servico): void
    {
        try {
            Mail::send('emails.servico-parecer-fiscal', [
                'servico' => $servico,
            ], function ($message) use ($emails, $subject) {
                $message->to($emails)
                    ->subject($subject);
            });
        } catch (\Exception $e) {
            Log::error('Erro ao enviar parecer final do fiscal', [
                'emails' => $emails,
                'subject' => $subject,
                'servico_id' => $servico->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
