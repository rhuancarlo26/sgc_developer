<?php

namespace App\Shared\Listeners;

use App\Models\UserAccess;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LogUserAccess
{
    public function __construct(protected Request $request) {}

    public function handle(Login $event): void
    {
        UserAccess::create([
            'user_id'      => $event->user->id,
            'ip_address'   => $this->request->ip(),
            'user_agent'   => $this->request->userAgent(),
            'logged_in_at' => now(),
        ]);
    }
}