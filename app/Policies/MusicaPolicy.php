<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Musica;
use Illuminate\Auth\Access\HandlesAuthorization;

class MusicaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Musica');
    }

    public function view(AuthUser $authUser, Musica $musica): bool
    {
        return $authUser->can('View:Musica');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Musica');
    }

    public function update(AuthUser $authUser, Musica $musica): bool
    {
        return $authUser->can('Update:Musica');
    }

    public function delete(AuthUser $authUser, Musica $musica): bool
    {
        return $authUser->can('Delete:Musica');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Musica');
    }

    public function restore(AuthUser $authUser, Musica $musica): bool
    {
        return $authUser->can('Restore:Musica');
    }

    public function forceDelete(AuthUser $authUser, Musica $musica): bool
    {
        return $authUser->can('ForceDelete:Musica');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Musica');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Musica');
    }

    public function replicate(AuthUser $authUser, Musica $musica): bool
    {
        return $authUser->can('Replicate:Musica');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Musica');
    }

}