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
        // $debugSQL = $entity::create([...$infos, 'user_id' => Auth::user()->id]);
        // dd($debugSQL);

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
    

    public function delete($entity, $infos)
    {
        // $newEntity  = new $entity;
        // $debugSQL = $newEntity->where($infos)->delete();
        // dd($debugSQL);

        try {
            $newEntity  = new $entity;
            $model      = $newEntity->where($infos)->delete();
            $type       = 'success';
            $content    = 'Registro excluido!';
        } catch (\Exception) {
            $model   = null;
            $type    = 'error';
            $content = 'Falha ao excluir!';
        }

        return [
            'model'   => $model,
            'request' => [
                'type'    => $type,
                'content' => $content,
            ]
        ];
    }

}
