<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaRelatorio extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_relatorios';
    protected $guarded = ['id', 'created_at'];

    public function status()
    {
        return $this->belongsTo(ServicoStatus::class, 'status_id');
    }

    public function resultado()
    {
        return $this->belongsTo(ServicoPmqaResultado::class, 'resultado_id');
    }
}
