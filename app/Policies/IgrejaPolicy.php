<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Igreja;
use Illuminate\Auth\Access\HandlesAuthorization;

class IgrejaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Igreja');
    }

    public function view(AuthUser $authUser, Igreja $igreja): bool
    {
        return $authUser->can('View:Igreja');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Igreja');
    }

    public function update(AuthUser $authUser, Igreja $igreja): bool
    {
        return $authUser->can('Update:Igreja');
    }

    public function delete(AuthUser $authUser, Igreja $igreja): bool
    {
        return $authUser->can('Delete:Igreja');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Igreja');
    }

    public function restore(AuthUser $authUser, Igreja $igreja): bool
    {
        return $authUser->can('Restore:Igreja');
    }

    public function forceDelete(AuthUser $authUser, Igreja $igreja): bool
    {
        return $authUser->can('ForceDelete:Igreja');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Igreja');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Igreja');
    }

    public function replicate(AuthUser $authUser, Igreja $igreja): bool
    {
        return $authUser->can('Replicate:Igreja');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Igreja');
    }

}