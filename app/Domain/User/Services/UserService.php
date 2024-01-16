<?php

namespace App\Domain\User\Services;

use App\Models\User;
use App\Shared\BaseClasses\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class UserService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = User::class;

    /**
     * Save a new User to the database and returns it
     *
     * @param array $userData
     * @return User
     */
    public function create(array $userData): User
    {
        return User::create($userData);
    }

    /**
     * Update the User on the database
     *
     * @param User $user
     * @param array $updatedData
     * @return User
     */
    public function update(User $user, array $updatedData): User
    {
        return tap($user)->update($updatedData);
    }

    /**
     * Set roles based on the array
     *
     * @param User $user
     * @param array $rolesIds
     * @return void
     */
    public function syncRoles(User $user, array $rolesIds): void
    {
        $user->roles()->sync($rolesIds);
    }

    /**
     * Create User and set its roles based on the array
     *
     * @param array $userData
     * @param array $rolesIds
     * @return User
     */
    public function createAndSyncRoles(array $userData, array $rolesIds): User
    {
        return \DB::transaction(function () use (&$userData, &$rolesIds) {
            $user = $this->create($userData);
            $this->syncRoles($user, $rolesIds);
            return $user;
        });
    }

    /**
     * Update User and set its roles based on the array
     *
     * @param User $user
     * @param array $updatedData
     * @param array $rolesIds
     * @return User
     */
    public function updateAndSyncRoles(User $user, array $updatedData, array $rolesIds): User
    {
        return \DB::transaction(function () use (&$user, &$updatedData, &$rolesIds) {
            $user = $this->update($user, $updatedData);
            $this->syncRoles($user, $rolesIds);
            return $user;
        });
    }
}
