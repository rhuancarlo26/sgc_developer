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
            $error = '';
        } catch (\Exception $e) {
            $model   = null;
            $type    = 'error';
            $content = 'Falha ao cadastrar!';
            $error   = $e->getMessage();
        }

        return [
            'model'   => $model,
            'request' => [
                'type'    => $type,
                'content' => $content,
                'error' => $error
            ]
        ];
    }

    public function update($entity, $infos, $id): array
    {
        try {
            $model   = $entity::find($id)->update([...$infos, 'user_id' => Auth::user()->id]);
            $type    = 'success';
            $content = 'Registro atualizado!';
        } catch (\Exception $e) {
            $model   = null;
            $type    = 'error';
            $content = $e->getMessage();
        }

        return [
            'model'   => $model,
            'request' => [
                'type'    => $type,
                'content' => $content,
            ]
        ];
    }


    public function delete($entity, $id): array
    {
        try {
            $entity::find($id)->delete();
            $type       = 'success';
            $content    = 'Registro excluido!';
        } catch (\Exception) {
            $type    = 'error';
            $content = 'Falha ao excluir!';
        }

        return [
            'request' => [
                'type'    => $type,
                'content' => $content,
            ]
        ];
    }

}
