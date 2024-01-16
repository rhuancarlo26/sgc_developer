<?php

namespace App\Domain\Role\Services;

use App\Domain\Auth\Services\PermissionService;
use App\Shared\BaseClasses\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Spatie\Permission\Models\Role;

class RoleService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Role::class;

    /**
     * Save a new Role to the database and returns it
     *
     * @param array $roleData
     * @return Role
     */
    public function create(array $roleData): Role
    {
        return Role::create($roleData);
    }

    /**
     * Update the Role on the database
     *
     * @param Role $role
     * @param array $updatedData
     * @return Role
     */
    public function update(Role $role, array $updatedData): Role
    {
        return tap($role)->update($updatedData);
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
     * @param array $roleData
     * @param array $permissionsNames
     * @return Role
     */
    public function createAndSyncPermissions(array $roleData, array $permissionsNames): Role
    {
        return \DB::transaction(function () use (&$roleData, &$permissionsNames) {

            $role = $this->create($roleData);
            $this->syncPermissions($role, $permissionsNames);

            return $role;
        });
    }

    /**
     * Update Role and set its permissions based on the names array
     *
     * @param Role $role
     * @param array $updatedData
     * @param array $permissionsNames
     * @return Role
     */
    public function updateAndSyncPermissions(Role $role, array $updatedData, array $permissionsNames): Role
    {
        return \DB::transaction(function () use (&$role, &$updatedData, &$permissionsNames) {

            $role = $this->update($role, $updatedData);
            $this->syncPermissions($role, $permissionsNames);

            return $role;

        });
    }
}
