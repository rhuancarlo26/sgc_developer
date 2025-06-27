<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\FaunaService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Models\SgcFaunaCampanhaAbios;

class FaunaController extends Controller
{
    protected $faunaService;

    public function __construct(FaunaService $faunaService)
    {
        $this->faunaService = $faunaService;
    }

    public function storeProfissional(Request $request, $contrato, $produto): RedirectResponse
    {
        Log::info('FaunaController: Recebendo requisição para salvar profissional', [
            'contrato' => $contrato,
            'produto' => $produto,
            'dados' => $request->all(),
        ]);

        $validated = $request->validate([
            'profissional' => 'required|string|max:255',
            'formacao' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:255',
            'cpf' => 'nullable|string|max:255|regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/',
            'email' => 'nullable|email|max:255',
            'curriculum_lattes' => 'nullable|string|max:255|url',
            'funcao' => 'nullable|string|max:255',
            'ctf' => 'nullable|string|max:255',
            'validade' => 'nullable|date',
            'conselho_de_classe' => 'required|string|in:Sim,Não',
            'numero_de_registro' => 'nullable|required_if:conselho_de_classe,Sim|integer',
            'status' => 'required|string|in:Ativo,Inativo',
            'observacao' => 'nullable|string',
        ]);

        try {
            $this->faunaService->salvarProfissional($contrato, $validated);
            return redirect()->back()->with('success', 'Profissional salvo com sucesso!');
        } catch (\Exception $e) {
            Log::error('FaunaController: Erro ao salvar profissional', [
                'contrato' => $contrato,
                'produto' => $produto,
                'erro' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao salvar profissional.']);
        }
    }

    public function salvarCampanha(Request $request, $contrato, $produto): RedirectResponse
    {
        Log::info('FaunaController: Recebendo requisição para salvar campanha', [
            'contrato' => $contrato,
            'produto' => $produto,
            'dados' => $request->all(),
        ]);

        $validated = $request->validate([
            'data_campanha_inicial' => 'nullable|date',
            'data_campanha_final' => 'nullable|date',
            'periodo' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
            'id_abio' => 'nullable|array',
            'id_abio.*' => 'integer|exists:fauna_config_abio,id',
            'cod_emp' => 'required|string|max:255',
            'subproduto' => 'required|string|max:255',
            'nao_se_aplica' => 'nullable|boolean',
            'profissionais' => 'nullable|array',
            'profissionais.*.profissional' => 'required_with:profissionais|string|max:255',
            'profissionais.*.grupo_faunistico' => 'required_with:profissionais|string|in:Avifauna,Herpetofauna,Mastofauna,Ictiofauna,Bentos',
            'modulos_amostrais' => 'nullable|array',
            'modulos_amostrais.*.data_cadastro' => 'nullable|date',
            'modulos_amostrais.*.tamanho_modulo' => 'nullable|in:1,2,3,4,5',
            'modulos_amostrais.*.uf' => 'nullable|string|size:2',
            'modulos_amostrais.*.municipio' => 'nullable|string|max:50',
            'modulos_amostrais.*.bioma' => 'nullable|string|max:30',
            'modulos_amostrais.*.fitofisionomia' => 'nullable|string',
            'modulos_amostrais.*.latitude_inicial' => 'nullable|numeric',
            'modulos_amostrais.*.longitude_inicial' => 'nullable|numeric',
            'modulos_amostrais.*.latitude_final' => 'nullable|numeric',
            'modulos_amostrais.*.longitude_final' => 'nullable|numeric',
            'modulos_amostrais.*.obs' => 'nullable|string',
            'modulos_amostrais.*.arquivo' => 'nullable|file|mimes:shp,zip|max:1024',
            'pontos_quelo_crocod' => 'nullable|array',
            'pontos_quelo_crocod.*.ponto_de_coleta' => 'required_without:nao_se_aplica|string',
            'pontos_quelo_crocod.*.nome_curso_hidrico' => 'required_without:nao_se_aplica|string',
            'pontos_quelo_crocod.*.coordenadas' => 'nullable|string',
            'pontos_quelo_crocod.*.bacia' => 'required_without:nao_se_aplica|string',
            'pontos_quelo_crocod.*.profundidade' => 'nullable|numeric',
            'pontos_quelo_crocod.*.largura' => 'required_without:nao_se_aplica|numeric',
            'pontos_quelo_crocod.*.tipo_substrato' => 'nullable|string',
            'pontos_cavernicola' => 'nullable|array',
            'pontos_cavernicola.*.cavidade' => 'required_without:nao_se_aplica|string',
            'pontos_cavernicola.*.latitude' => 'required_without:nao_se_aplica|numeric',
            'pontos_cavernicola.*.longitude' => 'required_without:nao_se_aplica|numeric',
            'pontos_cavernicola.*.distancia_eixo_rodovia' => 'required_without:nao_se_aplica|numeric',
            'pontos_cavernicola.*.formacao_associada' => 'required_without:nao_se_aplica|string',
            'pontos_cavernicola.*.temperatura_media_interna' => 'nullable|numeric',
            'pontos_cavernicola.*.temperatura_media_externa' => 'nullable|numeric',
            'pontos_cavernicola.*.umidade_relativa_interna' => 'nullable|numeric',
            'pontos_cavernicola.*.umidade_relativa_externa' => 'nullable|numeric',
            'metodologias' => 'nullable|array',
            'metodologias.*.grupo_faunistico' => 'required_with:metodologias|string|in:Avifauna,Herpetofauna,Mastofauna,Ictiofauna,Bentos',
            'metodologias.*.metodologia' => 'required_with:metodologias|string',
            'resultados' => 'nullable|array',
            'resultados.*.id_campanha' => 'required_with:resultados|integer',
            'resultados.*.modulo' => 'nullable|',
            'resultados.*.parcela' => 'nullable|',
            'resultados.*.id_armadilha' => 'nullable|integer',
            'resultados.*.grupo_amostrado' => 'nullable|',
            'resultados.*.data_registro' => 'nullable|',
            'resultados.*.hora_registro' => 'nullable|',
            'resultados.*.categoria' => 'nullable|',
            'resultados.*.classe' => 'nullable|',
            'resultados.*.ordem' => 'nullable|',
            'resultados.*.familia' => 'nullable|',
            'resultados.*.genero' => 'nullable|',
            'resultados.*.especie' => 'nullable|',
            'resultados.*.nome_comum' => 'nullable|',
            'resultados.*.sexo' => 'nullable|',
            'resultados.*.faixa_etaria' => 'nullable|',
            'resultados.*.qnt_individuos' => 'nullable|integer|min:0',
            'resultados.*.num_marcacao' => 'nullable|',
            'resultados.*.coletado' => 'nullable|',
            'resultados.*.num_tombamento' => 'nullable|',
            'resultados.*.dados_biometricos' => 'nullable|',
            'resultados.*.comp_total' => 'nullable|integer',
            'resultados.*.cabeca' => 'nullable|integer',
            'resultados.*.cauda' => 'nullable|integer',
            'resultados.*.femur' => 'nullable|integer',
            'resultados.*.orelha' => 'nullable|integer',
            'resultados.*.peso' => 'nullable|integer',
            'resultados.*.status_conservacao_federal' => 'nullable|',
            'resultados.*.status_conservacao_iucn' => 'nullable|',
        ]);

        try {
            $campanhaId = $this->faunaService->salvarCampanha($contrato, $validated);

            // Salvar os ABIOs na tabela sgc_fauna_campanha_abios
            if (!empty($validated['id_abio'])) {
                foreach ($validated['id_abio'] as $abioId) {
                    SgcFaunaCampanhaAbios::create([
                        'contrato_id' => $contrato,
                        'campanha_id' => $campanhaId,
                        'n_abio' => $abioId,
                    ]);
                }
            }

            return redirect()->route('sgc.contratada.produtos.index', [$contrato, $produto])
                ->with('success', 'Campanha salva com sucesso!');
        } catch (\Exception $e) {
            Log::error('FaunaController: Erro ao salvar campanha', [
                'contrato' => $contrato,
                'produto' => $produto,
                'erro' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao salvar campanha.']);
        }
    }

    public function storeResultados(Request $request, $contrato, $produto): RedirectResponse
    {
        Log::info('FaunaController: Recebendo requisição para salvar resultados', [
            'contrato' => $contrato,
            'produto' => $produto,
            'dados' => $request->all(),
        ]);

        $validated = $request->validate([
            'planilha' => 'required|file|mimes:xlsx,xls|max:10240', // Máximo 10MB
        ]);

        try {
            $this->faunaService->salvarResultados($contrato, $validated['planilha']);
            return redirect()->back()->with('success', 'Resultados salvos com sucesso!');
        } catch (\Exception $e) {
            Log::error('FaunaController: Erro ao salvar resultados', [
                'contrato' => $contrato,
                'produto' => $produto,
                'erro' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao salvar resultados.']);
        }
    }
}