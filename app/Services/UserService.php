<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\Models\User;
use App\Services\Traits\Deletable;
use App\Services\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class UserService
{
    use Searchable, Deletable;

    private readonly Model $model;

    public function __construct()
    {
        $this->model = new User();
    }

    /**
     * Save a new User to the database and returns it
     *
     * @param UserDTO $dto
     * @return User
     */
    public function create(UserDTO $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email
        ]);
    }

    /**
     * Update the User on the database
     *
     * @param User $user
     * @param UserDTO $dto
     * @return User
     */
    public function update(User $user, UserDTO $dto): User
    {
        return tap($user)->update([
            'name' => $dto->name,
            'email' => $dto->email
        ]);
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
     * @param UserDTO $dto
     * @param array $rolesIds
     * @return User
     */
    public function createAndSyncRoles(UserDTO $dto, array $rolesIds): User
    {
        return \DB::transaction(function () use (&$dto, &$rolesIds) {
            $user = $this->create($dto);
            $this->syncRoles($user, $rolesIds);
            return $user;
        });
    }

    /**
     * Update User and set its roles based on the array
     *
     * @param User $user
     * @param UserDTO $dto
     * @param array $rolesIds
     * @return User
     */
    public function updateAndSyncRoles(User $user, UserDTO $dto, array $rolesIds): User
    {
        return \DB::transaction(function () use (&$user, &$dto, &$rolesIds) {
            $user = $this->update($user, $dto);
            $this->syncRoles($user, $rolesIds);
            return $user;
        });
    }
}
