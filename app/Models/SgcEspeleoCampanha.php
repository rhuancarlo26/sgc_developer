<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcEspeleoCampanha extends Model
{
    use HasFactory;

    protected $table = 'sgc_espeleo_campanhas';

    protected $fillable = [
        'id_contrato',
        'id_campanha',
        'status',
        'versao_analise',
        'cod_emp',
        'subproduto',
        'subtrecho',
        'segmento',
        'extensao',
        'tipo_de_intervencao',
        'descricao',
        'bioma',
    ];

    protected $casts = [
        'versao_analise' => 'integer',
        'extensao' => 'decimal:2',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_contrato');
    }

    public function empreendimento()
    {
        return $this->belongsTo(SgcvwEmpreendimentos::class, 'cod_emp', 'cod_emp');
    }

    public function justificativas()
    {
        return $this->hasMany(SgcEspeleoJustificativa::class, 'campanha_id');
    }

    public function metodologia()
    {
        return $this->hasOne(SgcEspeleoMetodologia::class, 'campanha_id');
    }

    public function resultadoAnexos()
    {
        return $this->hasMany(SgcEspeleoResultadoAnexo::class, 'campanha_id');
    }

    public function anexos()
    {
        return $this->hasMany(SgcEspeleoAnexo::class, 'campanha_id');
    }

    public function campanhaProfissionais()
    {
        return $this->hasMany(SgcEspeleoCampanhaProfissional::class, 'campanha_id');
    }

    public function profissionais()
    {
        return $this->belongsToMany(
            SgcEspeleoProfissional::class,
            'sgc_espeleo_campanha_profissionais',
            'campanha_id',
            'profissional_id'
        );
    }

    public function estudosPosteriores()
    {
        return $this->hasMany(SgcEspeleoEstudosPosteriores::class, 'campanha_id');
    }

    public function campanhaLayers()
    {
        return $this->hasMany(SgcEspeleoCampanhaLayer::class, 'campanha_id');
    }

    public function mapLayers()
    {
        return $this->belongsToMany(
            MapLayer::class,
            'sgc_espeleo_campanha_layers',
            'campanha_id',
            'map_layer_id'
        )->withPivot('tipo')->withTimestamps();
    }

}
