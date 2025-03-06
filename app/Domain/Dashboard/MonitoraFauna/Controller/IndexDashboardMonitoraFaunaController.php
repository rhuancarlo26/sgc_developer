<?php

namespace App\Domain\Dashboard\MonitoraFauna\Controller;

use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class IndexDashboardMonitoraFaunaController extends Controller
{
  public function __construct() {}

  public function index(): Response
  {
    return Inertia::render('Dashboard/MonitoraFauna/Index');
  }
}
