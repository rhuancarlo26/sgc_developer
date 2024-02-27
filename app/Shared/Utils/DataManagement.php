<?php

namespace App\Shared\Utils;

use Illuminate\Support\Facades\Auth;

class DataManagement
{
    public function __construct()
    {
    }

    public function create($entity, $infos): array
    {
        try {
            $model   = $entity::create([...$infos, 'user_id' => Auth::user()->id]);
            $type    = 'success';
            $content = 'Registro cadastrado!';
        } catch (\Exception) {
            $model   = null;
            $type    = 'error';
            $content = 'Falha ao cadastrar!';
        }

        return [
            'model'   => $model,
            'request' => [
                'type'    => $type,
                'content' => $content,
            ]
        ];
    }

    public function update($entity, $infos, $id): array
    {
        try {
            $model   = $entity::find($id)->update([...$infos, 'user_id' => Auth::user()->id]);
            $type    = 'success';
            $content = 'Registro atualizado!';
        } catch (\Exception) {
            $model   = null;
            $type    = 'error';
            $content = 'Falha ao atualizar!';
        }

        return [
            'model'   => $model,
            'request' => [
                'type'    => $type,
                'content' => $content,
            ]
        ];
    }

    public function delete($entity, $infos)
    {

    }

}
