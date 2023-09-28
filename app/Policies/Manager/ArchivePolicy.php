<?php

namespace App\Policies\Manager;

use App\Libraries\Helper;
use App\Models\Archive;
use App\Models\Manager;
use Illuminate\Auth\Access\Response;

class ArchivePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Manager $manager): bool
    {
        return Helper::checkPermissionManager('manager.archive.index', $manager);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Manager $manager, Archive $archive): bool
    {
        return Helper::checkPermissionManager('manager.archive.destroy', $manager, $archive);
    }

    /**
     * @param Manager $manager
     * @param Archive $archive
     * @return bool
     */
    public function download(Manager $manager, Archive $archive): bool
    {
        return Helper::checkPermissionManager('manager.archive.download', $manager, $archive) && $archive->type == 'manager';
    }
}
