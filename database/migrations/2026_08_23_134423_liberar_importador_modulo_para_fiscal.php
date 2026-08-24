<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    private array $permissoes = [
        'modulos.importador.index',
        'modulos.importador.formulario',
        'modulos.importador.buscarDados',
        'modulos.importador.buscarDadosServico',
        'modulos.importador.buscarHistorico',
        'modulos.importador.aprovReprov',
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->permissoes as $permissao) {
            Permission::findOrCreate($permissao, 'web');
        }

        $roleFiscal = Role::query()
            ->where('name', 'Fiscal')
            ->where('guard_name', 'web')
            ->first();

        if ($roleFiscal) {
            $roleFiscal->givePermissionTo($this->permissoes);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roleFiscal = Role::query()
            ->where('name', 'Fiscal')
            ->where('guard_name', 'web')
            ->first();

        if ($roleFiscal) {
            $roleFiscal->revokePermissionTo($this->permissoes);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
