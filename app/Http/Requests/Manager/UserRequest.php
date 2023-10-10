<?php

namespace App\Http\Requests\Manager;

use App\Libraries\Helper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * @param Request $request
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        if ($request->getMethod() == 'PUT') {
            return Helper::checkPermissionManager('manager.user.edit', $this->user(), null, $request);
        }
        return Helper::checkPermissionManager('manager.user.create', $this->user(), null, $request);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $unique = ['required'];

        if (!is_null($user)) {
            $unique[] = Rule::unique('users')->ignore($user->id);
        } else {
            $unique[] = 'unique:users';
        }
        return [
            'role_group_id' => 'required|exists:user_role_groups,id',
            'username' => $unique,
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'is_active' => 'required'
        ];
    }
}
