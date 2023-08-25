<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function showLinkRequestForm()
    {
        return Inertia::render('Admin/Auth/ForgotPassword');
    }

    protected function validateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => [
                'required',
                Rule::exists('admins')->where(function ($query) {
                    $query->where('is_active', 1);
                }),
            ],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function broker()
    {
        return Password::broker('admins');
    }

    protected function credentials(Request $request)
    {
        return [
            'username' => $request->username,
            'is_active' => true
        ];
    }

}
