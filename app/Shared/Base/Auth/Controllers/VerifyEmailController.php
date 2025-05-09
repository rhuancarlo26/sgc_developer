<?php

namespace App\Shared\Base\Auth\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Shared\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()
                ->intended(RouteServiceProvider::HOME . '?verified=1')
                ->with('message', ['type' => 'info', 'content' => 'Email já havia sido verificado']);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()
            ->intended(RouteServiceProvider::HOME . '?verified=1')
            ->with('message', ['type' => 'success', 'content' => 'Email verificado!']);
    }
}
