<?php

namespace App\Http\Requests\User;

use App\Libraries\Helper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PersonRequest extends FormRequest
{
    /**
     * @param Request $request
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        if ($request->getMethod() == 'PUT') {
            return Helper::checkPermissionUser('user.person.edit', $this->user(), null, $request);
        }
        return Helper::checkPermissionUser('user.person.create', $this->user(), null, $request);
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'group_id' => 'required|exists:groups,id',
            'name' => 'required|max:191',
            'email' => 'required|max:191',
            'phone' => 'required|max:191',
        ];
    }
}
