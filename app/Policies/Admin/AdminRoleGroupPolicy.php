<?php

namespace App\Policies\Admin;

use App\Libraries\Helper;
use App\Models\Admin;
use App\Models\AdminRoleGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminRoleGroupPolicy
{
    use HandlesAuthorization;

    /**
     * @param Admin $admin
     * @return bool
     */
    public function viewAny(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.admin_role_group.index', $admin);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Admin $admin, AdminRoleGroup $adminRoleGroup): bool
    {
        return Helper::checkPermissionAdmin('admin.admin_role_group.show', $admin, $adminRoleGroup);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.admin_role_group.create', $admin);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Admin $admin, AdminRoleGroup $adminRoleGroup): bool
    {
        return Helper::checkPermissionAdmin('admin.admin_role_group.edit', $admin, $adminRoleGroup);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin $admin, AdminRoleGroup $adminRoleGroup): bool
    {
        return Helper::checkPermissionAdmin('admin.admin_role_group.destroy', $admin, $adminRoleGroup);
    }

    /**
     * @param Admin $admin
     * @return bool
     */
    public function download(Admin $admin):bool
    {
        return Helper::checkPermissionAdmin('admin.admin_role_group.download', $admin);
    }
}
