<?php

namespace App\Http\Requests\User;

use App\Libraries\Helper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PersonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(Request $request): bool
    {
        if ($request->getMethod() == 'PUT') {
            return Helper::checkPermissionUser('user.person.edit', $this->user(), null, $request);
        }
        return Helper::checkPermissionUser('user.person.create', $this->user(), null, $request);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
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
