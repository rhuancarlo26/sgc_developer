<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcvwSubprodutos extends Model
{
    use HasFactory;
    protected $fillable = [
        'cod_siac',
        'produto',
        'subproduto',
        'familia',
        'descricao_siac',
        'descricao_revisada',
        'und',
        'etapa',
        'contrato',
        'req_ext',
        'prazo_de_elaboracao',
        'qtd_contrato',
        'qtd_1_ta',
        'qtd_2_ta',
        'qtd_ose',
        'qtd_saldo_ose',
        'qtd_medido',
        'qtd_saldo_medido',
        'r_preco_unitario',
        'r_total_contrato',
        'r_1_ta',
        'r_2_ta',
        'r_ose',
        'r_saldo_ose',
        'r_medido',
        'r_saldo_a_medir',
    ];
}
