<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Customer;
use App\Traits\AuthenticationTrait;
use Illuminate\Http\Response;

class RegisterController extends Controller
{
    use AuthenticationTrait;

    public function register(RegisterRequest $request): Response
    {
        $customer = Customer::create();

        $user = $customer->user()->create($request->validated());

        return $this->authenticate($user, $request);
    }
}
