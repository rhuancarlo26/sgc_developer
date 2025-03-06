<?php

namespace App\Domain\Dashboard\MonAtpFauna\Controller;

use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class IndexDashboardMonAtpFaunaController extends Controller
{
  public function __construct() {}

  public function index(): Response
  {
    return Inertia::render('Dashboard/MonAtpFauna/Index');
  }
}
