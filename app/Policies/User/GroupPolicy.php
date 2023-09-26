<?php

namespace App\Policies\User;

use App\Libraries\Helper;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return Helper::checkPermissionUser('user.group.index', $user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Group $group): bool
    {
        return Helper::checkPermissionUser('user.group.show', $user, $group);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Helper::checkPermissionUser('user.group.create', $user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Group $group): bool
    {
        return Helper::checkPermissionUser('user.group.edit', $user, $group);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Group $group): bool
    {
        return Helper::checkPermissionUser('user.group.destroy', $user, $group);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function download(User $user):bool
    {
        return Helper::checkPermissionUser('user.group.download', $user);
    }
}
