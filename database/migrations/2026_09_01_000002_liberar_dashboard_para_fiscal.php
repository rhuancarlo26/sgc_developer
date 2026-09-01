<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    private string $permissao = 'dashboard';

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::findOrCreate($this->permissao, 'web');

        $roleFiscal = Role::query()
            ->where('name', 'Fiscal')
            ->where('guard_name', 'web')
            ->first();

        if ($roleFiscal) {
            $roleFiscal->givePermissionTo($this->permissao);
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
            $roleFiscal->revokePermissionTo($this->permissao);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
