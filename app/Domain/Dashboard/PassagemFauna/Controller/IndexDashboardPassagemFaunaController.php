<?php

namespace App\Domain\Dashboard\PassagemFauna\Controller;

use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class IndexDashboardPassagemFaunaController extends Controller
{
  public function __construct() {}

  public function index(): Response
  {
    return Inertia::render('Dashboard/PassagemFauna/Index');
  }
}
