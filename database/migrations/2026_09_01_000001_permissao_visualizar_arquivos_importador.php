<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    private array $permissoes = [
        'modulos.importador.visualizarFoto',
        'modulos.importador.visualizarAnexo',
    ];

    private string $permissaoBase = 'modulos.importador.formulario';

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->permissoes as $permissao) {
            Permission::findOrCreate($permissao, 'web');
        }

        Role::query()
            ->where('guard_name', 'web')
            ->get()
            ->filter(fn (Role $role) => $role->name === 'Fiscal' || $role->hasPermissionTo($this->permissaoBase))
            ->each(fn (Role $role) => $role->givePermissionTo($this->permissoes));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::query()
            ->where('guard_name', 'web')
            ->get()
            ->each(fn (Role $role) => $role->revokePermissionTo($this->permissoes));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
