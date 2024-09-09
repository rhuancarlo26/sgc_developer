<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaRelatorio extends Model
{
    use HasFactory;

    protected $table = 'relatorio';
    protected $guarded = ['id', 'created_at'];

    public function status()
    {
        return $this->belongsTo(ServicoStatus::class, 'fk_status');
    }

    public function resultado()
    {
        return $this->belongsTo(ServicoPmqaResultado::class, 'fk_resultado');
    }
}
