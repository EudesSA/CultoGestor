<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Ensaio;
use Illuminate\Auth\Access\HandlesAuthorization;

class EnsaioPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Ensaio');
    }

    public function view(AuthUser $authUser, Ensaio $ensaio): bool
    {
        return $authUser->can('View:Ensaio');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Ensaio');
    }

    public function update(AuthUser $authUser, Ensaio $ensaio): bool
    {
        return $authUser->can('Update:Ensaio');
    }

    public function delete(AuthUser $authUser, Ensaio $ensaio): bool
    {
        return $authUser->can('Delete:Ensaio');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Ensaio');
    }

    public function restore(AuthUser $authUser, Ensaio $ensaio): bool
    {
        return $authUser->can('Restore:Ensaio');
    }

    public function forceDelete(AuthUser $authUser, Ensaio $ensaio): bool
    {
        return $authUser->can('ForceDelete:Ensaio');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Ensaio');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Ensaio');
    }

    public function replicate(AuthUser $authUser, Ensaio $ensaio): bool
    {
        return $authUser->can('Replicate:Ensaio');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Ensaio');
    }

}