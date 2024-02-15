<?php

namespace App\Domain\Licenca\Condicionante\Controller;

use App\Models\Licenca;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class IndexCondicionanteController extends Controller
{
  public function __construct()
  {
  }

  public function index(Licenca $licenca): Response
  {
    return Inertia::render('Licenca/Condicionante/Index');
  }
}