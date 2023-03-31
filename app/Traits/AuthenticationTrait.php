<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait AuthenticationTrait
{
    public function authenticate(User $user, Request $request): Response
    {
        $credentials = $user->email
            ? ['email' => $user->email, 'password' => $request->password]
            : ['phone' => $user->phone, 'password' => $request->password];

        if (!auth()->attempt($credentials)
        ) {
            return response(['message' => __('un matched credentials')], 404);
        }

        return response([
            'token' => $user->createToken('auth')->plainTextToken,
            'user' => $user->load('user'),
//            'permissions' => $user->roles->load('permissions'),
            'message' => __('logged in successfully')
        ]);
    }

}
