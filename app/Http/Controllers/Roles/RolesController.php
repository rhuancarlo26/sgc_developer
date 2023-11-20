<?php

namespace App\Http\Controllers\Roles;

use App\DTOs\RoleDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\StoreRoleRequest;
use App\Http\Requests\Roles\UpdateRoleRequest;
use App\Services\RoleService;
use App\Utils\RouteUtils;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function __construct(private readonly RoleService $roleService)
    {
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $searchParams = $request->all('searchColumn', 'searchValue');

        return Inertia::render('Roles/Index', [
            'roles' => $this->roleService
                ->search(... $searchParams)
                ->where('name', '!=', 'Development')
                ->paginate()
                ->appends($searchParams)
        ]);
    }

    /**
     * @param Role $role
     * @return Response
     */
    public function form(Role $role): Response
    {
        return Inertia::render('Roles/Form', [
            'role' => $role->load('permissions'),
            'routes' => RouteUtils::getRoutesByMiddleware('route-permission')
        ]);
    }

    /**
     * @param StoreRoleRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = $this->roleService->createAndSyncPermissions(
            dto: RoleDTO::fromFormRequest($request),
            permissionsNames: $request->validated('permissions')
        );

        return to_route('cadastros.perfis.formulario', $role->id)->with('message', [
            'type' => 'success',
            'content' => "Perfil criado com sucesso"
        ]);

    }

    /**
     * @param Role $role
     * @param UpdateRoleRequest $request
     * @return RedirectResponse
     */
    public function update(Role $role, UpdateRoleRequest $request): RedirectResponse
    {
        $this->roleService->updateAndSyncPermissions(
            role: $role,
            dto: RoleDTO::fromFormRequest($request),
            permissionsNames: $request->get('permissions')
        );

        return back()->with('message', [
            'type' => 'success',
            'content' => "Perfil atualizado com sucesso"
        ]);
    }

    /**
     * @param Role $role
     * @return RedirectResponse
     */
    public function destroy(Role $role): RedirectResponse
    {
        $this->roleService->delete($role);

        return to_route('cadastros.perfis.listagem')->with('message', [
            'type' => 'info',
            'content' => "Perfil deletado com sucesso"
        ]);
    }
}
