<?php

namespace App\Http\Requests\Admin;

use App\Libraries\Helper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    /**
     * @param Request $request
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        if ($request->getMethod() == 'PUT') {
            return Helper::checkPermissionAdmin('admin.company.edit', $this->user(), null, $request);
        }
        return Helper::checkPermissionAdmin('admin.company.create', $this->user(), null, $request);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $company = $this->route('company');
        $managerUnique = ['required'];
        $userUnique = ['required'];

        if (!is_null($company)) {
            $managerUnique[] = Rule::unique('managers', 'username')->ignore($company->supervisor()->id);
        } else {
            $managerUnique[] = 'unique:managers,username';
        }
        if (!is_null($company)) {
            $userUnique[] = Rule::unique('users', 'username')->ignore($company->mainUser()->id);
        } else {
            $userUnique[] = 'unique:users,username';
        }
        return [
            'name' => 'required',
            'package_id' => 'required|exists:packages,id',
            'is_active' => 'nullable',
            'due_date' => 'nullable',
            'supervisor.username' => $managerUnique,
            'supervisor.first_name' => 'required',
            'supervisor.last_name' => 'required',
            'supervisor.email' => 'required',
            'supervisor.phone' => 'nullable|numeric',
            'mainUser.username' => $userUnique,
            'mainUser.first_name' => 'required',
            'mainUser.last_name' => 'required',
            'mainUser.email' => 'required',
            'mainUser.phone' => 'nullable|numeric',
        ];
    }
}
