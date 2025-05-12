<?php

namespace App\Domain\Sgc\Contratada\Ficha\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Ficha\Services\FichaService;
use Inertia\Inertia;
use App\Models\Contrato;
use Inertia\Response;
use Illuminate\Http\Request;

class FichaController extends Controller
{
    protected $fichaService;

    public function __construct(FichaService $fichaService)
    {
        $this->fichaService = $fichaService;
    }

    public function index($contrato)
    {
        $contratoObj = Contrato::find($contrato);

        if (!$contratoObj) {
            return Inertia::render('Sgc/Contratada/Ficha Contratual/Index', [
                'fichaData' => ['error' => 'Contrato não encontrado'],
                'contratoId' => $contrato,
            ]);
        }

        $numeroContrato = $contratoObj->numero_contrato; 
        $numeroContratoFormatado = preg_replace('/[^0-9]/', '', $numeroContrato); 

        $fichaData = $this->fichaService->getFichaData($numeroContratoFormatado);

        return Inertia::render('Sgc/Contratada/Ficha Contratual/Index', [
            'fichaData' => $fichaData,
            'contratoId' => $contrato,
            'contrato' => ['id' => $contratoObj->id], 
        ]);
    }
}


