<?php

namespace App\Policies\Admin;

use App\Libraries\Helper;
use App\Models\Admin;
use App\Models\Archive;
use Illuminate\Auth\Access\Response;

class ArchivePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin $admin): bool
    {

        return Helper::checkPermissionAdmin('admin.archive.index', $admin);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin $admin, Archive $archive): bool
    {
        return Helper::checkPermissionAdmin('admin.archive.destroy', $admin, $archive);
    }

    public function download(Admin $admin, Archive $archive): bool
    {
        return Helper::checkPermissionAdmin('admin.archive.download', $admin, $archive) && $archive->type == 'admin';
    }
}
