<?php

namespace App\Policies\User;

use App\Libraries\Helper;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return Helper::checkPermissionUser('user.user.index', $user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return Helper::checkPermissionUser('user.user.show', $user, $model);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Helper::checkPermissionUser('user.user.create', $user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return Helper::checkPermissionUser('user.user.edit', $user, $model);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return Helper::checkPermissionUser('user.user.destroy', $user, $model);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function download(User $user):bool
    {
        return Helper::checkPermissionUser('user.user.download', $user);
    }
}
