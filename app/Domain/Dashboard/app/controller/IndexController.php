<?php

namespace App\Domain\Dashboard\app\controller;

use App\Domain\Dashboard\app\services\DashboardService;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
  public function __construct(private readonly DashboardService $dashboardService) {}

  public function index(): Response
  {
    $response = $this->dashboardService->index();

    return Inertia::render('Dashboard', [
      ...$response
    ]);
  }
}
