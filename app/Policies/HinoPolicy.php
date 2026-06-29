<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Hino;
use Illuminate\Auth\Access\HandlesAuthorization;

class HinoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Hino');
    }

    public function view(AuthUser $authUser, Hino $hino): bool
    {
        return $authUser->can('View:Hino');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Hino');
    }

    public function update(AuthUser $authUser, Hino $hino): bool
    {
        return $authUser->can('Update:Hino');
    }

    public function delete(AuthUser $authUser, Hino $hino): bool
    {
        return $authUser->can('Delete:Hino');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Hino');
    }

    public function restore(AuthUser $authUser, Hino $hino): bool
    {
        return $authUser->can('Restore:Hino');
    }

    public function forceDelete(AuthUser $authUser, Hino $hino): bool
    {
        return $authUser->can('ForceDelete:Hino');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Hino');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Hino');
    }

    public function replicate(AuthUser $authUser, Hino $hino): bool
    {
        return $authUser->can('Replicate:Hino');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Hino');
    }

}