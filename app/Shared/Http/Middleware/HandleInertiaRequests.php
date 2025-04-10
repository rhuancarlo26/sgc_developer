<?php

namespace App\Shared\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user()?->load([
                    'roles' => fn($q) => $q->select('id', 'name'),
                    'roles.permissions:id,name'
                ])->append(['all_permissions']),
            ],
            'ziggy' => fn() => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'geoserver' => [
                'url' => config('geoserver.url'),
                'workspace' => config('geoserver.workspace')
            ],
            'app_url' => config('app.url'),
            'flash' => [
                'message' => fn() => $request->session()->get('message')
            ]
        ];
    }
}