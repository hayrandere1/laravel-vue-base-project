<?php

namespace App\Http\Requests\Admin;

use App\Libraries\Helper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PackageRequest extends FormRequest
{
    /**
     * @param Request $request
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        if ($request->getMethod() == 'PUT') {
            return Helper::checkPermissionAdmin('admin.package.edit', $this->user(), null, $request);
        }
        return Helper::checkPermissionAdmin('admin.package.create', $this->user(), null, $request);
    }

    //@todo:manager ve user role daha detaylı request hazırlamak lazım
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:191',
            'managerRoles' => 'array',
            'userRoles' => 'array',
        ];
    }
}
