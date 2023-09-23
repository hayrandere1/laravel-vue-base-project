<?php

namespace App\Policies\Manager;

use App\Libraries\Helper;
use App\Models\Manager;
use App\Models\ManagerRoleGroup;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ManagerRoleGroupPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Manager $manager): bool
    {
        return Helper::checkPermissionManager('manager.manager_role_group.index', $manager);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Manager $manager, ManagerRoleGroup $managerRoleGroup): bool
    {
        return Helper::checkPermissionManager('manager.manager_role_group.show', $manager, $managerRoleGroup);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Manager $manager): bool
    {
        return Helper::checkPermissionManager('manager.manager_role_group.create', $manager);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Manager $manager, ManagerRoleGroup $managerRoleGroup): bool
    {
        return Helper::checkPermissionManager('manager.manager_role_group.edit', $manager, $managerRoleGroup);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Manager $manager, ManagerRoleGroup $managerRoleGroup): bool
    {
        return Helper::checkPermissionManager('manager.manager_role_group.destroy', $manager, $managerRoleGroup);
    }

    /**
     * @param Manager $manager
     * @return bool
     */
    public function download(Manager $manager):bool
    {
        return Helper::checkPermissionManager('manager.manager_role_group.download', $manager);
    }
}
