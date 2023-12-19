<?php

namespace App\Domain\Roles\Services;

use App\Domain\Auth\Services\PermissionService;
use App\Domain\Roles\DTOs\RoleDTO;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RoleService
{
    use Searchable, Deletable;

    private readonly Model $model;

    public function __construct()
    {
        $this->model = new Role();
    }

    /**
     * Save a new Role to the database and returns it
     *
     * @param RoleDTO $dto
     * @return Role
     */
    public function create(RoleDTO $dto): Role
    {
        return Role::create([
            'name' => $dto->name,
            'guard_name' => $dto->guardName ?? 'web'
        ]);
    }

    /**
     * Update the Role on the database
     *
     * @param Role $role
     * @param RoleDTO $dto
     * @return Role
     */
    public function update(Role $role, RoleDTO $dto): Role
    {
        return tap($role)->update([
            'name' => $dto->name,
            'guard_name' => $dto->guardName ?? 'web'
        ]);
    }

    /**
     * Set permissions based on the names array
     * Names bust be aliases of valid routes with the RoutePermissionMiddleware
     *
     * @param Role $role
     * @param array $permissionsNames
     * @return void
     */
    public function syncPermissions(Role $role, array $permissionsNames): void
    {
        $permissionService = new PermissionService();

        $permissionService->updateRoutesPermissions();

        $role->syncPermissions($permissionService->getPermissionsIdsByNames($permissionsNames));
    }

    /**
     * Create Role and set its permissions based on the names array
     *
     * @param RoleDTO $dto
     * @param array $permissionsNames
     * @return Role
     */
    public function createAndSyncPermissions(RoleDTO $dto, array $permissionsNames): Role
    {
        return \DB::transaction(function () use (&$dto, &$permissionsNames) {

            $role = $this->create($dto);
            $this->syncPermissions($role, $permissionsNames);

            return $role;
        });
    }

    /**
     * Update Role and set its permissions based on the names array
     *
     * @param Role $role
     * @param RoleDTO $dto
     * @param array $permissionsNames
     * @return Role
     */
    public function updateAndSyncPermissions(Role $role, RoleDTO $dto, array $permissionsNames): Role
    {
        return \DB::transaction(function () use (&$role, &$dto, &$permissionsNames) {

            $role = $this->update($role, $dto);
            $this->syncPermissions($role, $permissionsNames);

            return $role;

        });
    }
}
