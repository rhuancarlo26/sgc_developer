<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaCampanha extends Model
{
    use HasFactory;

    protected $table = 'exec_campanhas';
    protected $guarded = ['id', 'created_at'];

    public function pontos()
    {
        return $this->belongsToMany(
            related: ServicoPmqaPonto::class,
            table: 'exec_campanha_ponto',
            foreignPivotKey: 'fk_exec_campanha',
            relatedPivotKey: 'fk_ponto'
        );
    }

    public function campanha_pontos()
    {
        return $this->hasMany(ServicoPmqaCampanhaPonto::class, 'fk_exec_campanha');
    }
}
