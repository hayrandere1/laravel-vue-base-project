<?php

namespace App\Policies\User;

use App\Libraries\Helper;
use App\Models\User;
use App\Models\UserRoleGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserRoleGroupPolicy
{
    use HandlesAuthorization;
    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return Helper::checkPermissionUser('user.user_role_group.index', $user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserRoleGroup $userRoleGroup): bool
    {
        return Helper::checkPermissionUser('user.user_role_group.show', $user, $userRoleGroup);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Helper::checkPermissionUser('user.user_role_group.create', $user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserRoleGroup $userRoleGroup): bool
    {
        return Helper::checkPermissionUser('user.user_role_group.edit', $user, $userRoleGroup);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserRoleGroup $userRoleGroup): bool
    {
        return Helper::checkPermissionUser('user.user_role_group.destroy', $user, $userRoleGroup);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function download(User $user):bool
    {
        return Helper::checkPermissionUser('user.user_role_group.download', $user);
    }
}
