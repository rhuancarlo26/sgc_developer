<?php

namespace App\Domain\Role\Controllers;

use App\Domain\Role\Requests\StoreRoleRequest;
use App\Domain\Role\Requests\UpdateRoleRequest;
use App\Domain\Role\Services\RoleService;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Utils\RouteUtils;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
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

        return Inertia::render('Role/Index', [
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
    public function create(Role $role): Response
    {
        return Inertia::render('Role/Form', [
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
            roleData: ['name' => $request->validated('name'), 'guard_name' => 'web'],
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
            updatedData: ['name' => $request->validated('name')],
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
