<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $userInfo = Auth::user();
        $userRoles = app('userRoles')->getRoles(Auth::user());
        $data = [
            'userInfo' => [
                'username' => $userInfo->username,
                'firstName' => $userInfo->first_name,
                'lastName' => $userInfo->last_name,
                'email' => $userInfo->email,
                'phone' => $userInfo->phone,
                'user_email_md5' => md5($userInfo->email)
            ],
            'userRoles' => $userRoles];
        return new JsonResponse($data);
    }
}
