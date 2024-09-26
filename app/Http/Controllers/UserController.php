<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FilesController;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Freshbitsweb\Laratables\Laratables;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Fetch data for users table.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIndexTable()
    {
        $this->authorize('viewAny', User::class);

        return Laratables::recordsOf(User::class);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.users.create', ['roles' => $this->getRoles()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\User\StoreUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->saveUser(new User, $request->validated());

        return redirect()->route('users.index')->with('toast_success', 'Registro guardado.');
    }

    /**
     * Redirect to edit view of the specified resource.
     *
     * @param  \App\Models\User                    $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(User $user)
    {
        return redirect()->route('users.edit', $user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User        $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', ['user' => $user, 'roles' => $this->getRoles()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\User\UpdateUserRequest $request
     * @param  \App\Models\User                          $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user = $this->saveUser($user, $request->validated());

        return redirect()->route('users.edit', $user)->with('toast_success', 'Registro actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User                    $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('toast_success', 'Registro eliminado.');
    }

    /**
     * Helper function to get the roles.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getRoles()
    {
        return Role::pluck('name', 'id');
    }

    /**
     * Handle user saving/updating logic.
     *
     * @param  \App\Models\User   $user
     * @param  array              $fields
     * @return \App\Models\User
     */
    protected function saveUser(User $user, array $fields)
    {
        $roles    = Arr::pull($fields, 'roles');
        $password = Arr::pull($fields, 'password');

        // Update the user (exclude avatar from fields)
        $user->fill(Arr::except($fields, 'avatar'));

        // Handle password update only if present
        if ($password) {
            $user->password = bcrypt($password);
        }

        $user->save();

        // Sync roles
        $user->roles()->sync($roles);

        // Handle avatar upload if present
        if (isset($fields['avatar']) && !FilesController::saveFile($fields['avatar'], $user, 'private', 'avatar')) {
            throw new \Exception('Error al guardar el avatar.');
        }

        // Handle other file uploads if necessary
        if (isset($fields['file']) && !FilesController::saveFile($fields['file'], $user, 'private')) {
            throw new \Exception('Error al guardar el archivo.');
        }

        return $user;
    }
}
