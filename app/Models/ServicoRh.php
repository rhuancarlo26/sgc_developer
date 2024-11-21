<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoRh extends Model
{
    use HasFactory;

    protected $table = 'servico_rh';
    protected $guarded = ['id', 'created_at'];
    public $timestamps = false;

    public function rh()
    {
        return $this->belongsTo(RecursoRh::class, 'id_rh');
    }
}
