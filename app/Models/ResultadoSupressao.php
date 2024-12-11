<?php

namespace App\Models;

use App\Domain\Servico\SupressaoVegetacao\Resultado\Strategy\Periodo\AnualStrategy;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Strategy\Periodo\MensalStrategy;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Strategy\Periodo\SemestralStrategy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResultadoSupressao extends Model
{
    use SoftDeletes;

    protected $table = 'resultado_supressao';

    protected $guarded = ['id'];

    protected $appends = ['mes', 'semestre', 'ano'];

    protected $casts = [
        'dt_inicio' => 'date:Y-m-d',
        'dt_final' => 'date:Y-m-d',
    ];

    public function ano(): Attribute
    {
        return Attribute::make(
            get: fn() => (new AnualStrategy())->getDate($this->dt_inicio)
        );
    }

    public function mes(): Attribute
    {
        return Attribute::make(
            get: fn() => (new MensalStrategy())->getDate($this->dt_inicio)
        );
    }

    public function semestre(): Attribute
    {
        return Attribute::make(
            get: fn() => (new SemestralStrategy())->getDate($this->dt_inicio)
        );
    }

    public function relatorios(): HasMany
    {
        return $this->hasMany(SupressaoRelatorio::class, 'fk_resultado');
    }

}
