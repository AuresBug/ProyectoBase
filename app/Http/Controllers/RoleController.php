<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Role;
use Freshbitsweb\Laratables\Laratables;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.roles.index');
    }

    /**
     * Fetch data for roles table.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIndexTable()
    {
        $this->authorize('viewAny', Role::class);

        return Laratables::recordsOf(Role::class);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.roles.create', ['permissions' => $this->getPermissions()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Role\StoreRoleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRoleRequest $request)
    {
        $role = $this->saveRole(new Role, $request->validated());

        return redirect()->route('roles.edit', $role)->with('toast_success', 'Registro guardado.');
    }

    /**
     * Redirect to edit view of the specified resource.
     *
     * @param  \App\Models\Role                    $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Role $role)
    {
        return redirect()->route('roles.edit', $role);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role        $role
     * @return \Illuminate\View\View
     */
    public function edit(Role $role)
    {
        return view('admin.roles.edit', [
            'role'        => $role,
            'permissions' => $this->getPermissions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Role\UpdateRoleRequest $request
     * @param  \App\Models\Role                          $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role = $this->saveRole($role, $request->validated());

        return redirect()->route('roles.edit', $role)->with('toast_success', 'Registro actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role                    $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index')->with('toast_success', 'Registro eliminado.');
    }

    /**
     * Helper function to get permissions.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getPermissions()
    {
        return Permission::pluck('name', 'id');
    }

    /**
     * Handle role saving/updating logic.
     *
     * @param  \App\Models\Role   $role
     * @param  array              $fields
     * @return \App\Models\Role
     */
    protected function saveRole(Role $role, array $fields)
    {
        $name        = Arr::only($fields, 'name');
        $permissions = Arr::get($fields, 'permissions', []);

        // Update role
        $role->fill($name)->save();

        // Sync permissions
        $role->syncPermissions($permissions);

        return $role;
    }
}
