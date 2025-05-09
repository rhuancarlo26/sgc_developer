<?php

namespace App\Domain\Sgc\Contratada\app\Controller;

use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Domain\Sgc\Contratada\app\Services\RelatorioService;
use Inertia\Inertia;
use Inertia\Response;
use App\Domain\Sgc\Contratada\app\Imports\YourImportClass;
use App\Domain\Sgc\Contratada\app\Services\ContratosService;
use App\Models\Contrato;
use App\Models\ContratoTipo;
use App\Models\DashexcelEmpreendimentos;
use App\Models\Rodovia;
use App\Models\SgcvwEmpreendimentos;
use App\Models\SgcvwEstudos;
use App\Models\Sgcvwempfases;
use App\Models\Uf;


class DashplanController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService, private readonly ContratosService $contratosService)
  {
  }
    //
    public function import(Request $request) {
        $file = $request->file('excel_file');
        Excel::import(new YourImportClass, $file);
        return redirect()->back()->with('success', 'Arquivo Excel importado com sucesso.');
    }
    public function index(ContratoTipo $tipo, Request $request): Response
    {
        $posts = DashexcelEmpreendimentos::all();

        $searchParams = $request->all('columns', 'value');

        $contratos = $this->contratosService->Contratos($tipo, $searchParams);

        $subprodutos_por_empreendimento = SgcvwEmpreendimentos::get();     //ampliar resultados
        $estudos = SgcvwEstudos::all();
        $fases = Sgcvwempfases::all();
        $ufs = Uf::all();
        $rodovias = Rodovia::all();

        return Inertia::render('Sgc/Contratada/Relatorio/PainelGerencial', [
            ...$contratos,
            'tipo' => $tipo,
            'posts' => $posts,
            'empreendimentos' => $subprodutos_por_empreendimento,
            'estudos' => $estudos,
            'fases' => $fases,
            'ufs' => $ufs,
            'rodovias' => $rodovias
        ]);
    }
    public function search(Request $request)
    {
        $query = DashexcelEmpreendimentos::query();

        if ($request->has('empreendimento')) {
            $query->where('empreendimento', 'like', '%' . $request->input('empreendimento') . '%');
        }

        if ($request->has('produto')) {
            $query->where('produto', 'like', '%' . $request->input('produto') . '%');
        }

        if ($request->has('subproduto')) {
            $query->where('subproduto', 'like', '%' . $request->input('subproduto') . '%');
        }

        if ($request->has('data')) {
            $query->whereDate('data', $request->input('data'));
        }

        $posts = $query->get();

        return response()->json($posts);
    }
    public function searchempreendimentos(Request $request)
    {
        $query = SgcvwEmpreendimentos::query();
    
        if ($request->has('cod_emp')) {
            $query->where('cod_emp', 'like', '%' . $request->input('cod_emp') . '%');
        }
        if ($request->has('criticidade') and $request->input('criticidade') !== null) {
            $campo = $request->input('criticidade');
            $query->where($campo, 'like', '%' . $request->input('prioridade') . '%');
        }
        if ($request->has('prioridade') && !empty($request->input('prioridade')) && $request->input('prioridade') !== 'Normal') {
            $query->where('prioridade', $request->input('prioridade'));
        }
        // Filtro por ose_sei (Com OSE ou Sem OSE)
        if ($request->has('ose_sei') && !empty($request->input('ose_sei'))) {
            $oseSeiFiltro = $request->input('ose_sei');
            if ($oseSeiFiltro === 'com_ose') {
                $query->whereNotNull('ose_sei')->where('ose_sei', '!=', '');
            } elseif ($oseSeiFiltro === 'sem_ose') {
                $query->where(function ($q) {
                    $q->whereNull('ose_sei')->orWhere('ose_sei', '');
                });
            }
        }
    
        $posts = $query->get();
        return response()->json($posts);
    }
    
    public function novafase(Request $request)
    {
        // Cria o registro na tabela
        Sgcvwempfases::create([
            'fase' => $request->fase,
            'status' => $request->status,
            'periodo' => $request->periodo,
            'empreendimento_id' => $request->empreendimento_id,
        ]);

        return redirect()->back()->with('message', 'Fase cadastrada com sucesso!');
    }
    public function updatefase(Request $request)
    {
        // Cria o registro na tabela
        $Fase = Sgcvwempfases::findOrFail($request->id);
        $Fase->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('message', 'Fase cadastrada com sucesso!');
    }
}
