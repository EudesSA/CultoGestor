<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Cantor;
use Illuminate\Auth\Access\HandlesAuthorization;

class CantorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Cantor');
    }

    public function view(AuthUser $authUser, Cantor $cantor): bool
    {
        return $authUser->can('View:Cantor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Cantor');
    }

    public function update(AuthUser $authUser, Cantor $cantor): bool
    {
        return $authUser->can('Update:Cantor');
    }

    public function delete(AuthUser $authUser, Cantor $cantor): bool
    {
        return $authUser->can('Delete:Cantor');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Cantor');
    }

    public function restore(AuthUser $authUser, Cantor $cantor): bool
    {
        return $authUser->can('Restore:Cantor');
    }

    public function forceDelete(AuthUser $authUser, Cantor $cantor): bool
    {
        return $authUser->can('ForceDelete:Cantor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Cantor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Cantor');
    }

    public function replicate(AuthUser $authUser, Cantor $cantor): bool
    {
        return $authUser->can('Replicate:Cantor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Cantor');
    }

}