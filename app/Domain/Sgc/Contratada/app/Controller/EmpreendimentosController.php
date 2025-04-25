<?php

namespace App\Domain\Sgc\Contratada\app\Controller;
use App\Shared\Http\Controllers\Controller;
use App\Models\SgcvwSubprodutos;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Domain\Sgc\Contratada\app\Services\RelatorioService;
use Inertia\Inertia;
use Inertia\Response;
// use App\Imports\YourImportClass;
use App\Domain\Sgc\Contratada\app\Imports\YourImportClass;
use App\Domain\Sgc\Contratada\app\Services\ContratosService;
use App\Domain\Sgc\Contratada\app\Services\EmpreendimentosService;
use App\Models\Contrato;
use App\Models\ContratoTipo;
use App\Models\DashexcelEmpreendimentos;
use App\Models\SgcvwEmpreendimentos;
use App\Models\SgcvwEstudos;
use App\Models\Sgcvwempfases;
use App\Models\ChangeLog;

use Illuminate\Support\Facades\Schema;



class EmpreendimentosController extends Controller
{
    public function __construct(
        private readonly EmpreendimentosService $empreendimentoService, private readonly ContratosService $contratosService)
    {
    }
    //
    public function import(Request $request, $empreendimento) {
        $file = $request->file('excel_file');
        Excel::import(new YourImportClass, $file);
        return redirect()->back()->with('success', 'Arquivo Excel importado com sucesso.');
    }
    /**
     * Empreendimentos: Sistema de Edição Interativo no Front-End --------------------------------------------------------
     */
    public function editavel(): Response
    {
      $empreendimentos = SgcvwEmpreendimentos::all();
      return Inertia::render('Sgc/Contratada/Relatorio/Empreendimento/Edicao', [
        'empreendimentos' => $empreendimentos,
      ]);
    }
    public function editavelestudos(): Response
    {
      $empreendimentos = SgcvwEstudos::paginate(50);
      return Inertia::render('Sgc/Contratada/Relatorio/Empreendimento/EdicaoEstudos', [
        'empreendimentos' => $empreendimentos,
      ]);
    }
    public function editavelprodutos(): Response
    {
      $empreendimentos = SgcvwSubprodutos::paginate(50);
      return Inertia::render('Sgc/Contratada/Relatorio/Empreendimento/EdicaoProdutos', [
        'empreendimentos' => $empreendimentos,
      ]);
    }
    public function updatecampo(Request $request, $id)
    {
        $empreendimento = SgcvwEmpreendimentos::findOrFail($id);

        // Obtém a chave e o valor da requisição
        $campo = array_keys($request->all())[0]; // Pega a chave no request
        $valor = $request->input($campo); // Pega o valor

        // Se o campo existe
        if (Schema::hasColumn('sgcvw_empreendimentos', $campo)) {
            $old_value = $empreendimento->getOriginal($campo);
            $empreendimento->update([$campo => $valor]);
            // if ($oldValue != $newValue) {
                ChangeLog::create([
                    'user_id'    => auth()->id(),
                    'table_name' => 'sgcvw_empreendimentos',
                    'record_id'  => $id,
                    'field'      => $campo,
                    'old_value'  => $old_value,
                    'new_value'  => $valor,
                    'status'     => 'updated',
                ]);
            // }
            // return response()->json(['success' => true, 'message' => 'Campo atualizado com sucesso!']);
            return redirect()->back()->with('success', 'Empreendimento atualizado com sucesso!');
        }

        return response()->json(['success' => false, 'message' => 'Campo inválido.'], 400);
    }
    public function updatecampoestudos(Request $request, $id)
    {
        $empreendimento = SgcvwEstudos::findOrFail($id);
        $campo = array_keys($request->all())[0]; // Pega a chave no request
        $valor = $request->input($campo); // Pega o valor
        if (Schema::hasColumn('sgcvw_estudos', $campo)) {
            $old_value = $empreendimento->getOriginal($campo);
            $empreendimento->update([$campo => $valor]);
            ChangeLog::create([
                'user_id'    => auth()->id(),
                'table_name' => 'sgcvw_estudos',
                'record_id'  => $id,
                'field'      => $campo,
                'old_value'  => $old_value,
                'new_value'  => $valor,
                'status'     => 'updated',
            ]);
            return redirect()->back()->with('success', 'Estudo atualizado com sucesso!');
            // return response()->json(['success' => true, 'message' => 'Campo válido.'], 400);
        }
        return response()->json(['success' => false, 'message' => 'Campo inválido.'], 400);
    }
    public function updatecampoprodutos(Request $request, $id)
    {
        $empreendimento = SgcvwSubprodutos::findOrFail($id);
        $campo = array_keys($request->all())[0]; // Pega a chave no request
        $valor = $request->input($campo); // Pega o valor
        if (Schema::hasColumn('sgcvw_subprodutos', $campo)) {
            $old_value = $empreendimento->getOriginal($campo);
            $empreendimento->update([$campo => $valor]);
            ChangeLog::create([
                'user_id'    => auth()->id(),
                'table_name' => 'sgcvw_subprodutos',
                'record_id'  => $id,
                'field'      => $campo,
                'old_value'  => $old_value,
                'new_value'  => $valor,
                'status'     => 'updated',
            ]);
            return redirect()->back()->with('success', 'Produto atualizado com sucesso!');
            // return response()->json(['success' => true, 'message' => 'Campo válido.'], 400);
        }
        return response()->json(['success' => false, 'message' => 'Campo inválido.'], 400);
    }
    public function index(ContratoTipo $tipo, Request $request, $empreendimento): Response
    {
        $posts = SgcvwEmpreendimentos::all();
        $subprodutos_por_empreendimento = SgcvwEmpreendimentos::where('id', $empreendimento)->get();
        $empreendimentos2 = SgcvwEmpreendimentos::where('id', $empreendimento)->first(); //Para a aba Dados gerais
        $estudos = SgcvwEstudos::all();
        $subprodutos = SgcvwSubprodutos::all();
        $fases = Sgcvwempfases::where('empreendimento_id', $empreendimento)->get();

        $searchParams = $request->all('columns', 'value');

        $contratos = $this->contratosService->Contratos($tipo, $searchParams);
        #############################################################################################################
        // filtros em estudos para a timeline - Família EIA
        $where_familia_eia = [
            'cod_emp' => $empreendimentos2->cod_emp,
            'familia' => 'EIA'
        ];
        #############################################################################################################
        // filtros em estudos para a timeline - Família EIA
        $fm_eia_estudos_empreendimento = SgcvwEstudos::where($where_familia_eia)->get();
        // $fm_eia_estudos_familia_status = SgcvwEstudos::where($where_familia_eia)->whereNull('familia')->doesntExist();
        // $fm_eia_estudos_familia_va_sei = SgcvwEstudos::where($where_familia_eia)->whereNull('versao_aceita_sei')->doesntExist();
        // $fm_eia_estudos_familia_rq_ext = SgcvwEstudos::where($where_familia_eia)->whereNull('req_ext_sei')->doesntExist();
        // $fm_eia_estudos_fa_aut_ext_sei = SgcvwEstudos::where($where_familia_eia)->whereNull('aut_ext_sei')->doesntExist();
        // $fm_eia_estudos_f_data_req_ext = SgcvwEstudos::where($where_familia_eia)->max('req_ext_data');
        // $fm_eia_estudos_fa_aut_ext_dta = SgcvwEstudos::where($where_familia_eia)->max('aut_ext_data');
        #############################################################################################################
        // filtros em estudos para a timeline - Família PBA
        $where_familia_pba = [
            'cod_emp' => $empreendimentos2->cod_emp,
            'familia' => 'PBA'
        ];
        // Família PBA ESTUDOS
        $fm_pba_estudos_empreendimento = SgcvwEstudos::where($where_familia_pba)->get();
        // $fm_pba_estudos_familia_status = SgcvwEstudos::where($where_familia_pba)->whereNull('familia')->doesntExist();
        // $fm_pba_estudos_familia_va_sei = SgcvwEstudos::where($where_familia_pba)->whereNull('versao_aceita_sei')->doesntExist();
        // $fm_pba_estudos_familia_rq_ext = SgcvwEstudos::where($where_familia_pba)->whereNull('req_ext_sei')->doesntExist();
        // $fm_pba_estudos_fa_aut_ext_sei = SgcvwEstudos::where($where_familia_pba)->whereNull('aut_ext_sei')->doesntExist();
        // $fm_pba_estudos_f_data_req_ext = SgcvwEstudos::where($where_familia_pba)->max('req_ext_data');
        // $fm_pba_estudos_fa_aut_ext_dta = SgcvwEstudos::where($where_familia_pba)->max('aut_ext_data');
        #############################################################################################################
        // filtros em estudos para a timeline - Item Edital 3.1.1
        $where_itemedital_311 = [
            'cod_emp' => $empreendimentos2->cod_emp,
            'item_edital' => '3.1.1'
        ];
        $abio_emp_estudos_311 = SgcvwEstudos::where($where_itemedital_311)->get();
        #############################################################################################################
        // filtros em estudos para a timeline - Familia ASV
        $where_familia_asv = [
            'cod_emp' => $empreendimentos2->cod_emp,
            'familia' => 'ASV'
        ];
        $asv_emp_estudos = SgcvwEstudos::where($where_familia_asv)->get();
        #############################################################################################################
        // filtros em estudos para a timeline - Item Edital 5.3.1
        $iphan_emp_estudos_521 = SgcvwEstudos::where(['cod_emp' => $empreendimentos2->cod_emp])->where('item_edital', 'like', '%5.2.1%')->get();
        $iphan_emp_estudos_531 = SgcvwEstudos::where(['cod_emp' => $empreendimentos2->cod_emp])->where('item_edital', 'like', '%5.3.1%')->get();

        return Inertia::render('Sgc/Contratada/Relatorio/Empreendimento/Index', [
            ...$contratos,
            'tipo' => $tipo,
            'posts' => $posts,
            'empreendimentos' => $subprodutos_por_empreendimento,
            'estudos' => $estudos,
            'subprodutos' => $subprodutos,
            'empreendimentos2' =>$empreendimentos2,
            'fases' => $fases,
            // EIA
            'fm_eia_estudos_empreendimento' => $fm_eia_estudos_empreendimento,
            // PBA
            'fm_pba_estudos_empreendimento' => $fm_pba_estudos_empreendimento,
            // 3.1.1
            'abio_emp_estudos_311' => $abio_emp_estudos_311,
            // ASV
            'asv_emp_estudos' => $asv_emp_estudos,
            // IPHAN
            'iphan_emp_estudos_521' => $iphan_emp_estudos_521,
            'iphan_emp_estudos_531' => $iphan_emp_estudos_531,
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
    // searchempreendimentos
    public function searchempreendimentos(Request $request)
    {
        $query = SgcvwEmpreendimentos::query();

        if ($request->has('cod_emp')) {
            $query->where('cod_empd', 'like', '%' . $request->input('cod_emp') . '%');
        }
        // echo '<pre>';
        // print_r($request);
        // echo '</qpre>';
        if ($request->has('criticidade') and $request->input('criticidade') !== "") {
            if ($request->input('criticidade') == "criticidade_ibama_oema") {
                $query->where('criticidade_ibama_oema', 'like', '%' . $request->input('prioridade') . '%');
            }
        }
        // $query->where('criticidade_ibama_oema', 'like', '%lt%');

        $posts = $query->get();

        return response()->json($posts);
    }

    public function deleteEmpreendimento($id)
    {
        // Valida se o ID do empreendimento foi enviado

        try {
            // Encontra o empreendimento pelo ID e deleta
            $empreendimento = SgcvwEmpreendimentos::findOrFail($id);
            $empreendimento->delete();

            // Retorna uma resposta de sucesso
            return redirect()->back()->with('success', 'Empreendimento deletado com sucesso.');
        } catch (\Exception $e) {
            // Caso ocorra um erro, retorna uma mensagem de erro
            return redirect()->back()->with('error', 'Erro ao deletar empreendimento: ' . $e->getMessage());
        }
    }
}
