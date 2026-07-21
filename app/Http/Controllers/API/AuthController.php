<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request)
    {
       $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);
        $token = $user->createToken('lms_token')->plainTextToken;

        return $this->success(
            'User register successfully .',
            [
                'user' => new UserResource($user),
                'token' => $token,
            ],
            201);
    }

    public function login(LoginRequest $request)
    {
        if(!Auth::attempt($request->validated()))
            {
                return $this->error(
                    'Invalid email and password .',
                    [],401);
            }
            $user = Auth::user();
            $token = $user->createToken('lms_token')->plainTextToken;
            return $this->success(
                'Login successful .',
                [
                'user' => new UserResource($user),
                'token' => $token,
                ]
            );
    }
          public function logout()
    {
        request()->user()->currentAccessToken()->delete();

        return $this->success(
            'Logged out successfully.'
        );
    }

    /**
     * Current User
     */
    public function me()
    {
        return $this->success(
            'User profile.',
            new UserResource(request()->user())
        );
    }
}
