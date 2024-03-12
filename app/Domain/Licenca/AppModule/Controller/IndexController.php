<?php

namespace App\Domain\Licenca\AppModule\Controller;

use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function index(): Response
    {
        return Inertia::render(component: 'Licenca/Index');
    }
}
