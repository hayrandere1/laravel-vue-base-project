<?php

namespace App\Http\Requests\Manager;

use App\Libraries\Helper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserRoleGroupRequest extends FormRequest
{
    /**
     * @param Request $request
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        if ($request->getMethod() == 'PUT') {
            return Helper::checkPermissionManager('manager.user_role_group.edit', $this->user(), null, $request);
        }
        return Helper::checkPermissionManager('manager.user_role_group.create', $this->user(), null, $request);
    }


    //@todo:user role daha detaylı request hazırlamak lazım
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:191',
            'userRoles' => 'array',
        ];
    }
}
