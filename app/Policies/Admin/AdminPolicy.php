<?php

namespace App\Policies\Admin;

use App\Libraries\Helper;
use App\Models\Admin;
use Illuminate\Auth\Access\Response;

class AdminPolicy
{
    /**
     * @param Admin $admin
     * @return bool
     */
    public function viewAny(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.admin.index', $admin);
    }

    /**
     * @param Admin $admin
     * @param Admin $model
     * @return bool
     */
    public function view(Admin $admin, Admin $model): bool
    {
        return Helper::checkPermissionAdmin('admin.admin.show', $admin, $model);
    }

    /**
     * @param Admin $admin
     * @return bool
     */
    public function create(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.admin.create', $admin);
    }

    /**
     * @param Admin $admin
     * @param Admin $model
     * @return bool
     */
    public function update(Admin $admin, Admin $model): bool
    {
        return Helper::checkPermissionAdmin('admin.admin.edit', $admin, $model);
    }

    /**
     * @param Admin $admin
     * @param Admin $model
     * @return bool
     */
    public function delete(Admin $admin, Admin $model): bool
    {
        return Helper::checkPermissionAdmin('admin.admin.destroy', $admin, $model);
    }

    /**
     * @param Admin $admin
     * @return bool
     */
    public function download(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.admin.download', $admin);
    }
}
