<?php

namespace App\Policies;

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_service_request');
    }

    public function view(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->can('view_service_request');
    }

    public function create(User $user): bool
    {
        return $user->can('create_service_request');
    }

    public function update(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->can('update_service_request');
    }

    public function delete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->can('delete_service_request');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_service_request');
    }

    public function forceDelete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->can('force_delete_service_request');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_service_request');
    }

    public function restore(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->can('restore_service_request');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_service_request');
    }

    public function replicate(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->can('replicate_service_request');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_service_request');
    }
}
