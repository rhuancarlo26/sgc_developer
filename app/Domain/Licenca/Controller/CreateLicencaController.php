<?php

namespace App\Domain\Licenca\Controller;

use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CreateLicencaController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Licenca/Form');
    }
}
