<?php

namespace App\Http\Requests\Manager;

use App\Libraries\Helper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ManagerRequest extends FormRequest
{
    /**
     * @param Request $request
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        if ($request->getMethod() == 'PUT') {
            return Helper::checkPermissionManager('manager.manager.edit', $this->user(), null, $request);
        }
        return Helper::checkPermissionManager('manager.manager.create', $this->user(), null, $request);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $manager = $this->route('manager');
        $unique = ['required'];

        if (!is_null($manager)) {
            $unique[] = Rule::unique('managers')->ignore($manager->id);
        } else {
            $unique[] = 'unique:managers';
        }
        return [
            'role_group_id' => 'required|exists:manager_role_groups,id',
            'username' => $unique,
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'is_active' => 'required'
        ];
    }
}
