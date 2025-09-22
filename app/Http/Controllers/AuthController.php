<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            if(!Auth::guard('web')->attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'data' => null,
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login Success',
                'data' => [
                    'token' => $token,
                    'user' => new UserResource($user),
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function me() {
        try {
            $user = Auth::user();
            return response()->json([
                'message' => 'User Profile finded',
                'data'=> new UserResource($user),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
