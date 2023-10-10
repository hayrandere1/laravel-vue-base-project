<?php

namespace App\Http\Requests\Manager;

use App\Libraries\Helper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ManagerRoleGroupRequest extends FormRequest
{
    /**
     * @param Request $request
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        if ($request->getMethod() == 'PUT') {
            return Helper::checkPermissionManager('manager.manager_role_group.edit', $this->user(), null, $request);
        }
        return Helper::checkPermissionManager('manager.manager_role_group.create', $this->user(), null, $request);
    }

    //@todo:manager role daha detaylı request hazırlamak lazım
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:191',
            'managerRoles' => 'array',
        ];
    }
}
