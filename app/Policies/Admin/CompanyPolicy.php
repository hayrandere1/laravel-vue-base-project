<?php

namespace App\Policies\Admin;

use App\Libraries\Helper;
use App\Models\Admin;
use App\Models\Company;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.company.index', $admin);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Admin $admin, Company $company): bool
    {
        return Helper::checkPermissionAdmin('admin.company.show', $admin, $company);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.company.create', $admin);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Admin $admin, Company $company): bool
    {
        return Helper::checkPermissionAdmin('admin.company.edit', $admin, $company);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin $admin, Company $company): bool
    {
        return Helper::checkPermissionAdmin('admin.company.destroy', $admin, $company);
    }

    /**
     * @param Admin $admin
     * @return bool
     */
    public function download(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.company.download', $admin);
    }
}
