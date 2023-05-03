<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Association;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssociationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->can('view_any_association');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Association  $association
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Association $association)
    {
        return $user->can('view_association');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('create_association');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Association  $association
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Association $association)
    {
        return $user->can('update_association');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Association  $association
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Association $association)
    {
        return $user->can('delete_association');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function deleteAny(User $user)
    {
        return $user->can('delete_any_association');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Association  $association
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Association $association)
    {
        return $user->can('force_delete_association');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDeleteAny(User $user)
    {
        return $user->can('force_delete_any_association');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Association  $association
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Association $association)
    {
        return $user->can('restore_association');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restoreAny(User $user)
    {
        return $user->can('restore_any_association');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Association  $association
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function replicate(User $user, Association $association)
    {
        return $user->can('replicate_association');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reorder(User $user)
    {
        return $user->can('reorder_association');
    }

}
