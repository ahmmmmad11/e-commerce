<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use Illuminate\Http\Response;

class UpdatePasswordController extends Controller
{
    public function update(UpdatePasswordRequest $request): Response
    {
        $request->user()->update(['password' => $request->new_password]);

        return response(['message' => __('password has been updated successfully')]);
    }
}
