<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Informativo;
use Illuminate\Auth\Access\HandlesAuthorization;

class InformativoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Informativo');
    }

    public function view(AuthUser $authUser, Informativo $informativo): bool
    {
        return $authUser->can('View:Informativo');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Informativo');
    }

    public function update(AuthUser $authUser, Informativo $informativo): bool
    {
        return $authUser->can('Update:Informativo');
    }

    public function delete(AuthUser $authUser, Informativo $informativo): bool
    {
        return $authUser->can('Delete:Informativo');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Informativo');
    }

    public function restore(AuthUser $authUser, Informativo $informativo): bool
    {
        return $authUser->can('Restore:Informativo');
    }

    public function forceDelete(AuthUser $authUser, Informativo $informativo): bool
    {
        return $authUser->can('ForceDelete:Informativo');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Informativo');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Informativo');
    }

    public function replicate(AuthUser $authUser, Informativo $informativo): bool
    {
        return $authUser->can('Replicate:Informativo');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Informativo');
    }

}