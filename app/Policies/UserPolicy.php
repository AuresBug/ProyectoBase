<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User                        $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        // Permite ver cualquier usuario si tiene el permiso 'users.index'
        return $user->can('users.index');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User                        $user
     * @param  \App\Models\User                        $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model)
    {
        // Permite ver un usuario específico si tiene el permiso 'users.show'
        return $user->can('users.show');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User                        $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // Permite crear un usuario si tiene el permiso 'users.create'
        return $user->can('users.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User                        $user
     * @param  \App\Models\User                        $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $model)
    {
        // Permite actualizar un usuario si tiene el permiso 'users.edit'
        return $user->can('users.edit');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User                        $user
     * @param  \App\Models\User                        $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $model)
    {
        // Permite eliminar un usuario si tiene el permiso 'users.destroy' y no está intentando eliminarse a sí mismo
        return $user->can('users.destroy') && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User                        $user
     * @param  \App\Models\User                        $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, User $model)
    {
        // Permite restaurar un usuario si tiene el permiso 'users.destroy'
        return $user->can('users.destroy');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User                        $user
     * @param  \App\Models\User                        $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        // Permite eliminar permanentemente un usuario si tiene el permiso 'users.destroy'
        return $user->can('users.destroy');
    }
}
