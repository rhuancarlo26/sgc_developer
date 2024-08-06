<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaPonto extends Model
{
    use HasFactory;

    protected $table = 'config_pontos';
    protected $guarded = ['id', 'created_at'];

    public function vinculado()
    {
        return $this->hasOne(related: ServicoPmqaListaPonto::class, foreignKey: 'fk_ponto');
    }

    public function campanhas()
    {
        return $this->belongsToMany(
            related: ServicoPmqaPonto::class,
            table: 'exec_campanha_ponto',
            foreignPivotKey: 'fk_ponto',
            relatedPivotKey: 'fk_exec_campanha'
        );
    }

    public function lista()
    {
        return $this->hasOneThrough(
            related: ServicoPmqaParametroLista::class,
            through: ServicoPmqaListaPonto::class,
            firstKey: 'fk_ponto',
            secondKey: 'id',
            localKey: 'id',
            secondLocalKey: 'fk_lista'
        );
    }
}
