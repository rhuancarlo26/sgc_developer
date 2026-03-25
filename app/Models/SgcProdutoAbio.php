<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcProdutoAbio extends Model
{
    protected $table = 'sgc_produto_abios';
    protected $fillable = ['id_produto', 'id_abio'];

    public function abio()
    {
        return $this->belongsTo(ServicoMonitoraFaunaConfigAbio::class, 'id_abio');
    }

    public function produto()
    {
        return $this->belongsTo(SgcProdutos::class, 'id_produto');
    }
}