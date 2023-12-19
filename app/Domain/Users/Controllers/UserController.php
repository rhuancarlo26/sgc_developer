<?php

namespace App\Domain\Users\Controllers;

use App\Domain\Roles\Services\RoleService;
use App\Domain\Users\DTOs\UserDTO;
use App\Domain\Users\Requests\StoreUserRequest;
use App\Domain\Users\Requests\UpdateUserRequest;
use App\Domain\Users\Services\UserService;
use App\Models\User;
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

        return Inertia::render('Users/Index', [
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
        return Inertia::render('Users/Form', [
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
            dto: UserDTO::fromFormRequest($request),
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
            dto: UserDTO::fromFormRequest($request),
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
