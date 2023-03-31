<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Traits\AuthenticationTrait;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    use AuthenticationTrait;

    public function login(LoginRequest $request): Response
    {
        $user = User::where('email', $request->username)->orWhere('phone', $request->username)->first();

        return !$user
            ? response(['message' => __('user not found')], 404)
            : $this->authenticate($user, $request);
    }
}
