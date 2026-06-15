<?php

namespace App\Shared\Base\Auth\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Models\UserAccess;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserAccessController extends Controller
{
    public function index(Request $request): Response
    {
        $accesses = UserAccess::query()
            ->with('user:id,name,email')
            ->select(['user_id', 'ip_address', 'user_agent', 'logged_in_at'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', fn($q) =>
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                );
            })
            ->when($request->date_from, fn($q, $v) => $q->where('logged_in_at', '>=', $v))
            ->when($request->date_to,   fn($q, $v) => $q->where('logged_in_at', '<=', $v . ' 23:59:59'))
            ->latest('logged_in_at')
            ->paginate(50)
            ->withQueryString();

        return Inertia::render('Base/User/UserAcess', [
            'accesses' => $accesses,
            'filters'  => $request->only(['search', 'date_from', 'date_to']),
        ]);
    }
}