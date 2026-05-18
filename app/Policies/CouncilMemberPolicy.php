<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CouncilMember;
use Illuminate\Auth\Access\HandlesAuthorization;

class CouncilMemberPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_council::member');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CouncilMember $councilMember): bool
    {
        return $user->can('view_council::member');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_council::member');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CouncilMember $councilMember): bool
    {
        return $user->can('update_council::member');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CouncilMember $councilMember): bool
    {
        return $user->can('delete_council::member');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_council::member');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, CouncilMember $councilMember): bool
    {
        return $user->can('force_delete_council::member');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_council::member');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, CouncilMember $councilMember): bool
    {
        return $user->can('restore_council::member');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_council::member');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, CouncilMember $councilMember): bool
    {
        return $user->can('replicate_council::member');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_council::member');
    }
}
