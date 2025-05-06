<?php

namespace App\Domain\Sgc\Contratada\Quantitativos\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Quantitativos\Services\QuantitativosService;
use Inertia\Inertia;
use App\Models\Contrato;
use App\Models\SgcvwSubprodutos;
use Inertia\Response;
use Illuminate\Http\Request;

class QuantitativosController extends Controller
{
    protected $quantitativosService;

    public function __construct(QuantitativosService $quantitativosService)
    {
        $this->quantitativosService = $quantitativosService;
    }

    public function index($contrato)
    {
        $contratoObj = Contrato::find($contrato);

        if (!$contratoObj) {
            return Inertia::render('Sgc/Contratada/Quantitativos/Index', [
                'quantitativosData' => ['error' => 'Contrato não encontrado'],
                'contratoId' => $contrato,
            ]);
        }

        $quantitativosData = SgcvwSubprodutos::where('contrato_id', $contrato)->get();

        return Inertia::render('Sgc/Contratada/Quantitativos/Index', [
            'quantitativosData' => $quantitativosData,
            'contratoId' => $contrato,
            'contrato' => ['id' => $contratoObj->id], 
        ]);
    }
}