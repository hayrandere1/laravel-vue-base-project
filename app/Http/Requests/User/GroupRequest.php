<?php

namespace App\Http\Requests\User;

use App\Libraries\Helper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class GroupRequest extends FormRequest
{
    /**
     * @param Request $request
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        if ($request->getMethod() == 'PUT') {
            return Helper::checkPermissionUser('user.group.edit', $this->user(), null, $request);
        }
        return Helper::checkPermissionUser('user.group.create', $this->user(), null, $request);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:191',
        ];
    }
}
