<?php

namespace App\Http\Requests\Admin;

use App\Libraries\Helper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(Request $request)
    {
        if ($request->getMethod() == 'PUT') {
            return Helper::checkPermissionAdmin('admin.company.edit', $this->user(), null, $request);
        }
        return Helper::checkPermissionAdmin('admin.company.create', $this->user(), null, $request);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        /*
            'mainUserPhones' => 'nullable|array',
            'packet_id' => 'required|exists:packets,id',
            'max_user_count' => 'required',
            'timezone_name' => 'required',
        */
        $company = $this->route('company');
        $unique = ['required'];

        if (!is_null($company)) {
            $unique[] = Rule::unique('managers', 'username')->ignore($company->supervisor()->id);
        } else {
            $unique[] = 'unique:managers,username';
        }
        return   [
            'name' => 'required',
            'is_active' => 'nullable',
            'due_date' => 'nullable',
            'supervisor.username' => $unique,
            'supervisor.first_name' => 'required',
            'supervisor.last_name' => 'required',
            'supervisor.email' => 'required',
            'supervisor.phone' => 'nullable|numeric',
        ];
    }
}
