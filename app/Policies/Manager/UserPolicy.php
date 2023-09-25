<?php

namespace App\Policies\Manager;

use App\Libraries\Helper;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Manager $manager): bool
    {
        return Helper::checkPermissionManager('manager.user.index', $manager);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Manager $manager, User $user): bool
    {
        return Helper::checkPermissionManager('manager.user.show', $manager, $user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Manager $manager): bool
    {
        return Helper::checkPermissionManager('manager.user.create', $manager);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Manager $manager, User $user): bool
    {
        return Helper::checkPermissionManager('manager.user.edit', $manager, $user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Manager $manager, User $user): bool
    {
        return Helper::checkPermissionManager('manager.user.destroy', $manager, $user);
    }

    /**
     * @param Manager $manager
     * @return bool
     */
    public function download(Manager $manager): bool
    {
        return Helper::checkPermissionManager('manager.user.download', $manager);
    }
}
