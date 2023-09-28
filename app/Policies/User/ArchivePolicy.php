<?php

namespace App\Policies\User;

use App\Libraries\Helper;
use App\Models\Archive;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArchivePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return Helper::checkPermissionUser('user.archive.index', $user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Archive $archive): bool
    {
        return Helper::checkPermissionUser('user.archive.destroy', $user, $archive);
    }

    /**
     * @param User $user
     * @param Archive $archive
     * @return bool
     */
    public function download(User $user, Archive $archive): bool
    {
        return Helper::checkPermissionUser('user.archive.download', $user, $archive) && $archive->type == 'user';
    }
}
