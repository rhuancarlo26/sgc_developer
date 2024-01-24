<?php

namespace App\Shared\Base\User\Controllers;

use App\Models\User;
use App\Shared\Base\Role\Services\RoleService;
use App\Shared\Base\User\Requests\StoreUserRequest;
use App\Shared\Base\User\Requests\UpdateUserRequest;
use App\Shared\Base\User\Services\UserService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly RoleService $roleService
    )
    {
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $searchParams = $request->all('searchColumn', 'searchValue');

        return Inertia::render('Base/User/Index', [
            'users' => $this->userService
                ->search(... $searchParams)
                ->with(['roles'])
                ->paginate()
                ->appends($searchParams)
        ]);
    }

    /**
     * @param User $user
     * @return Response
     */
    public function create(User $user): Response
    {
        return Inertia::render('Base/User/Form', [
            'user' => $user->load('roles'),
            'roles' => $this->roleService->all()
        ]);
    }

    /**
     * @param StoreUserRequest $request
     * @return RedirectResponse
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $this->userService->createAndSyncRoles(
            userData: $request->only(['name', 'email']),
            rolesIds: $request->validated('roles')
        );

        return to_route('cadastros.usuarios.formulario', $user->id)->with('message', [
            'type' => 'success',
            'content' => "Usuário criado com sucesso"
        ]);
    }

    /**
     * @param User $user
     * @param UpdateUserRequest $request
     * @return RedirectResponse
     */
    public function update(User $user, UpdateUserRequest $request): RedirectResponse
    {
        $this->userService->updateAndSyncRoles(
            user: $user,
            updatedData: $request->only(['name', 'email']),
            rolesIds: $request->validated('roles')
        );

        return back()->with('message', [
            'type' => 'success',
            'content' => "Usuário atualizado com sucesso"
        ]);
    }

    /**
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->userService->delete($user);

        return to_route('cadastros.usuarios.listagem')->with('message', [
            'type' => 'info', 'content' => "Usuário deletado com sucesso"
        ]);
    }
}
