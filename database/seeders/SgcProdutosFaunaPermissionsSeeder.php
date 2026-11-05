<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SgcProdutosFaunaPermissionsSeeder extends Seeder
{
    /** Additive provisioning; run explicitly after reviewing the target roles. */
    public function run(): void
    {
        $read = [
            'sgc.contratada.produtos.fauna.atropelamento.modelo',
            'sgc.contratada.produtos.fauna.modelos.download',
            'sgc.contratada.produtos.fauna.simplificada.show',
            'sgc.contratada.produtos.index',
            'sgc.contratada.produtos.show',
        ];
        $fiscal = [
            'sgc.contratada.produtos.analise',
            'sgc.contratada.produtos.aprovarTudo',
            'sgc.contratada.produtos.fauna.simplificada.analise',
            'sgc.contratada.produtos.fauna.simplificada.aprovar',
            'sgc.contratada.produtos.fauna.simplificada.reprovar',
            'sgc.contratada.produtos.finalizarAvaliacao',
            'sgc.contratada.produtos.modulos.configuracoes.delete',
            'sgc.contratada.produtos.modulos.configuracoes.formulario',
            'sgc.contratada.produtos.modulos.configuracoes.gerar-planilha-modelo',
            'sgc.contratada.produtos.modulos.configuracoes.index',
            'sgc.contratada.produtos.modulos.configuracoes.processar-campos-planilha',
            'sgc.contratada.produtos.modulos.configuracoes.store',
            'sgc.contratada.produtos.modulos.configuracoes.update',
            'sgc.contratada.produtos.reprovarTudo',
            'sgc.contratada.produtos.salvarAnalise',
        ];
        $contractor = [
            'sgc.contratada.produtos.anexo.destroy',
            'sgc.contratada.produtos.arquivar',
            'sgc.contratada.produtos.comentario',
            'sgc.contratada.produtos.comentario.destroy',
            'sgc.contratada.produtos.create',
            'sgc.contratada.produtos.destroy',
            'sgc.contratada.produtos.edit',
            'sgc.contratada.produtos.fauna.simplificada.anexos.upload',
            'sgc.contratada.produtos.fauna.simplificada.edit',
            'sgc.contratada.produtos.fauna.simplificada.update',
            'sgc.contratada.produtos.profissional.destroy',
            'sgc.contratada.produtos.profissional.store',
            'sgc.contratada.produtos.rascunho.etapa',
            'sgc.contratada.produtos.rascunho.inicializar',
            'sgc.contratada.produtos.rascunho.submeter',
            'sgc.contratada.produtos.restaurar',
            'sgc.contratada.produtos.store',
            'sgc.contratada.produtos.update',
        ];
        $existingReaders = Role::query()->where('guard_name', 'web')
            ->whereHas('permissions', fn ($query) => $query->where('name', 'sgc.contratada.produtos.index'))
            ->get();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        foreach (array_unique([...$read, ...$fiscal, ...$contractor]) as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        foreach ($existingReaders as $role) {
            // Preserva consulta. Permissoes de autoria devem ser atribuidas aos perfis definidos no destino.
            $role->givePermissionTo($read);

        }
        $role = Role::query()->where('name', 'Fiscal')->where('guard_name', 'web')->first();
        if ($role) $role->givePermissionTo([...$read, ...$fiscal]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
