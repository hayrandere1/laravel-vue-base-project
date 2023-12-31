<?php

namespace App\Http\Requests\Admin;

use App\Libraries\Helper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminRequest extends FormRequest
{
    /**
     * @param Request $request
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        if ($request->getMethod() == 'PUT') {
            return Helper::checkPermissionAdmin('admin.admin.edit', $this->user(), null, $request);
        }
        return Helper::checkPermissionAdmin('admin.admin.create', $this->user(), null, $request);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $admin = $this->route('admin');
        $unique = ['required'];

        if (!is_null($admin)) {
            $unique[] = Rule::unique('admins')->ignore($admin->id);
        } else {
            $unique[] = 'unique:admins';
        }
        return [
            'role_group_id' => 'required|exists:admin_role_groups,id',
            'username' => $unique,
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'is_active' => 'required'
        ];
    }
}
