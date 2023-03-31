<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(User $user): Response
    {
        return response(['data' => $user->load('user')]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfileRequest $request, User $user): Response
    {
        $user->update(['name' => $request->name]);

        $user->user->update(['profile_image' => $request->profile_image]);

        return response([
            'data' => $user->load('user'),
            'message' => __('profile has been updated successfully')
        ]);
    }
}
