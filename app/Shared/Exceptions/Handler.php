<?php

namespace App\Shared\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);
        $status = $response->status();

        if (!app()->environment(['local', 'testing']) && ($status >= 500 && $status <= 599)) {
            return back()->with('message', [
                'type' => 'error',
                'content' => 'Não foi possível processar esta solicitação no momento. Por favor, tente novamente.'
            ]);
        }

        return $response;
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
