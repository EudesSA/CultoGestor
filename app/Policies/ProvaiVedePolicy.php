<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProvaiVede;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProvaiVedePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProvaiVede');
    }

    public function view(AuthUser $authUser, ProvaiVede $provaiVede): bool
    {
        return $authUser->can('View:ProvaiVede');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProvaiVede');
    }

    public function update(AuthUser $authUser, ProvaiVede $provaiVede): bool
    {
        return $authUser->can('Update:ProvaiVede');
    }

    public function delete(AuthUser $authUser, ProvaiVede $provaiVede): bool
    {
        return $authUser->can('Delete:ProvaiVede');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProvaiVede');
    }

    public function restore(AuthUser $authUser, ProvaiVede $provaiVede): bool
    {
        return $authUser->can('Restore:ProvaiVede');
    }

    public function forceDelete(AuthUser $authUser, ProvaiVede $provaiVede): bool
    {
        return $authUser->can('ForceDelete:ProvaiVede');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProvaiVede');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProvaiVede');
    }

    public function replicate(AuthUser $authUser, ProvaiVede $provaiVede): bool
    {
        return $authUser->can('Replicate:ProvaiVede');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProvaiVede');
    }

}