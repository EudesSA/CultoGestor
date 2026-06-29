<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Culto;
use Illuminate\Auth\Access\HandlesAuthorization;

class CultoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Culto');
    }

    public function view(AuthUser $authUser, Culto $culto): bool
    {
        return $authUser->can('View:Culto');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Culto');
    }

    public function update(AuthUser $authUser, Culto $culto): bool
    {
        return $authUser->can('Update:Culto');
    }

    public function delete(AuthUser $authUser, Culto $culto): bool
    {
        return $authUser->can('Delete:Culto');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Culto');
    }

    public function restore(AuthUser $authUser, Culto $culto): bool
    {
        return $authUser->can('Restore:Culto');
    }

    public function forceDelete(AuthUser $authUser, Culto $culto): bool
    {
        return $authUser->can('ForceDelete:Culto');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Culto');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Culto');
    }

    public function replicate(AuthUser $authUser, Culto $culto): bool
    {
        return $authUser->can('Replicate:Culto');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Culto');
    }

}