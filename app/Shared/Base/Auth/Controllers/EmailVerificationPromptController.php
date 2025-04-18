<?php

namespace App\Shared\Base\Auth\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Shared\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(RouteServiceProvider::HOME)
                    : Inertia::render('Base/Auth/VerifyEmail', ['status' => session('status')]);
    }
}
