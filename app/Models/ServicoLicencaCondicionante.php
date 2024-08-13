<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoLicencaCondicionante extends Model
{
    use HasFactory;

    protected $table = 'servico_licenca_condicionante';
    protected $guarded = ['id', 'created_at'];
    protected $appends = ['segmentos'];

    public function licenca()
    {
        return $this->belongsTo(Licenca::class, 'licenca_id');
    }

    public function condicionante()
    {
        return $this->belongsTo(LicencaCondicionante::class, 'condicionante_id');
    }

    public function segmentos(): Attribute
    {
        return Attribute::make(
            get: function () {
                $segmentos = [];

                // foreach ($this->licenca['segmentos'] as $value) {
                //     $rodovias = Rodovia::where('rodovia', $value['rodovia'])
                //         ->where(function ($query) use ($value) {
                //             $query->where('uf_id', $value['uf_inicial_id'])
                //                 ->orWhere('uf_id', $value['uf_final_id']);
                //         })
                //         ->get();

                //     $ufs = Uf::where('id', $value['uf_inicial_id'])->orWhere('id', $value['uf_final_id'])->get();

                //     foreach ($rodovias as $rodovia) {
                //         if (!isset($segmentos[$rodovia->rodovia])) {
                //             $segmentos[$rodovia->rodovia] = $rodovia->toArray();
                //             $segmentos[$rodovia->rodovia]['ufs'] = [];
                //         }

                //         foreach ($ufs as $uf) {
                //             if (!in_array($uf->id, array_column($segmentos[$rodovia->rodovia]['ufs'], 'id'))) {
                //                 $segmentos[$rodovia->rodovia]['ufs'][] = $uf->toArray();
                //             }
                //         }
                //     }
                // }

                return $segmentos;
            }
        );
    }
}
