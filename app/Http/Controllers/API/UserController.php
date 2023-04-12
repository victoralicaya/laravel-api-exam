<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * User Registration
     *
     * @param Request $request
     */
    public function registration(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        $token = $user->createToken('Exam')->accessToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token
        ], 200);
    }

    /**
     * User Login
     *
     * @param Request $request
     */
    public function login(UserLoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('Exam')->accessToken;

            return response()->json([
                'user' => new UserResource($user),
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                'message' => 'User Not Found'
            ], 404);
        }
    }

    /**
     * Get Loggedin User
     *
     * @param User $user
     */
    public function user()
    {
        return new UserResource(User::find(Auth::user()->id));
    }

    /**
     * User Logout
     */
    public function logout()
    {
        Auth::user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json([], 204);
    }
}
