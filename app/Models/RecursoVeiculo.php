<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecursoVeiculo extends Model
{
    use HasFactory;

    protected $table = 'veiculos';
    protected $guarded = ['id', 'created_at'];
    protected $appends = ['group_data_km'];

    protected $casts = [
        'alugado' => 'bool'
    ];

    public function codigo(): BelongsTo
    {
        return $this->belongsTo(RecursoVeiculoCodigo::class, 'cod_veiculos');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(RecursoVeiculoDocumento::class, 'cod_veiculo');
    }

    public function quilometragens(): HasMany
    {
        return $this->hasMany(RecursoVeiculoQuilometragem::class, 'recurso_veiculo_id');
    }

    protected function groupDataKm(): Attribute
    {
        return Attribute::make(
            get: function () {
                $groupedData = [];
                $km_total = 0;

                foreach ($this->quilometragens->toArray() as $value) {

                    $monthYear = date('Y-m-d', strtotime($value['mes_referencia']));

                    if (!isset($groupedData[$monthYear])) {
                        $groupedData[$monthYear] = [];
                        $groupedData[$monthYear]['total'] = 0;
                    }

                    array_push($groupedData[$monthYear], $value);
                    $groupedData[$monthYear]['total'] += $value['km_referencia'];

                    $km_total += $value['km_referencia'];
                }

                return [
                    'grouped_data' => $groupedData,
                    'km_total' => $km_total
                ];
            }
        );
    }
}
